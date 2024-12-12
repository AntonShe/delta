<?php

namespace common\models\shelf;

use common\models\AbstractRepository;
use common\models\genre\GenreEntity;
use common\models\IRepository;
use common\models\Pagination;
use common\models\product\ProductRepository;
use common\models\product\ProductService;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\ArrayShape;
use yii\db\Exception;

class ShelfRepository extends AbstractRepository
{
    private Pagination $pagination;
    private ProductRepository $productRepository;

    protected array $fieldsMap = [
        'start_date' => 'startDate',
        'end_date' => 'endDate',
        'url_name' => 'urlName',
        'is_active' => 'isActive',
    ];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];
    protected array $availableOrders = [
        'id',
        'sort',
    ];

    public function __construct()
    {
        $this->entity = new ShelfEntity();
        $this->productRepository = new ProductRepository();
        $this->pagination = Pagination::getInstance();

        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'IsActive',
            'Simple' => [
                'params' => [
                    'id'
                ]
            ],
        ]);
    }

    public function read(bool $isActive = false): array
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);
        $output = [];

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
            $output['pagination'] = $this->pagination->getData();
        }

        $shelves = $query->asArray()->all();

        foreach ($shelves as &$shelf) {
            $shelf = $this->convertField($shelf);
            $shelf['products'] = $this->getProducts($shelf['id'], isActive: $isActive);
            $this->convertDate($shelf);
        }

        $output['shelves'] = $shelves;

        return $output;
    }

    public function create(): array
    {
        try {
            $this->entity->setScenario(self::SCENARIO_CREATE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();
            $this->entity->load($this->convertField($this->params, true));

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new Exception('Создание не удалось');
            }

            // Связанные поля
            $this->saveRelatedFields($this->entity->id, $connection);

            $transaction->commit();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
            $transaction->rollBack();
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    public function update(int $id): array
    {
        try {
            $this->entity = ShelfEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new Exception('Обновление не удалось');
            }

            // Связанные поля
            $this->saveRelatedFields($id, $connection);

            $transaction->commit();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
            $transaction->rollBack();
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    public function delete(int $id): array
    {
        try {
            $this->entity = ShelfEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_DELETE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            if (!$this->entity->delete()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Обновление не удалось');
            }
            
            $this->deleteRelatedFields($id, $connection);

            $transaction->commit();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
            $transaction->rollBack();
        } catch (\Throwable $e) {
            $this->setErrors($e->getMessage());
            $transaction->rollBack();
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    private function getProducts($id, bool $isActive = false): array
    {
        try {
            $active = $isActive ? ' AND p.active = 1 AND quantity > 0' : '';
            $productIds = (array)$this->entity::getDb()->createCommand("
                SELECT product_id
                FROM products_trading_shelves pts
                JOIN products p ON p.id = pts.product_id
                WHERE trading_shelf_id = $id $active 
                ORDER BY sort
            ")->queryColumn();
            return array_values(array_unique($productIds));
        } catch (\Exception) {}

        return [];
    }

    private function deleteRelatedFields(int $id, &$connection): void
    {
        if ($id) {
            $connection->createCommand("
                DELETE FROM products_trading_shelves WHERE trading_shelf_id = $id
            ")->execute();
        }
    }

    /**
     * @throws Exception
     */
    private function saveRelatedFields(int $id, &$connection): void
    {
        if (isset($this->params['products']) && $this->params['products']) {
            try {
                $this->productRepository->setOrder([]);
                $this->productRepository->setParams([
                    'id' => $this->params['products'],
                    'withPagination' => false
                ]);
                $ids = array_column($this->productRepository->getProducts()['products'], 'id');
                $ids = array_intersect($this->params['products'], $ids);

                if (empty($ids)) {
                    throw new Exception("Неверные id товаров");
                }

                $connection->createCommand("
                    DELETE FROM products_trading_shelves WHERE trading_shelf_id = $id
                ")->execute();

                $connection->createCommand("
                    INSERT INTO products_trading_shelves (trading_shelf_id, product_id, sort, date_created, date_updated) 
                    {$this->getRelatedValuesInsertString($id, $ids)}
                ")->execute();
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    private function getRelatedValuesInsertString(int $id, array $ids): string
    {
        $values = [];
        foreach ($ids as $key => $relatedId) {
            $values[] = "({$id}, {$relatedId}, {$key}, current_date(), current_date())";
        }
        return "VALUES " . implode(', ', $values) ;
    }

    private function convertDate(array &$shelf): void
    {
        $shelf['startDate'] = date_format(new \DateTime($shelf['startDate']), 'Y-m-d');
        $shelf['endDate'] = date_format(new \DateTime($shelf['endDate']), 'Y-m-d');
    }
}