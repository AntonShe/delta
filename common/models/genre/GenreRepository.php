<?php

namespace common\models\genre;

use common\models\AbstractRepository;
use common\models\product\ProductRepository;
use yii\db\Exception;


class GenreRepository extends AbstractRepository
{
    protected array $orders = ['level' => 'asc', 'sort' => 'asc'];
    protected array $fieldsMap = [
        'parent_id' => 'parentId',
        'is_course' => 'isCourse',
        'on_main' => 'onMain',
    ];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];
    protected array $availableOrders = [
        'id',
        'name',
        'level',
        'sort',
    ];

    public function __construct()
    {
        $this->entity = new GenreEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Words',
            'ProductId' => [
                'relativeField' => 'genre',
            ],
            'Simple' => [
                'params' => ['id', 'level', 'parent_id', 'is_course', 'on_main']
            ],
        ]);
    }

    public function getGenres(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);
        $genres = [];

        foreach ($query->asArray()->all() as $item) {
            $data = $this->convertField($item);
            if ($data['onMain']) {
                $data['onMainInfo'] = $this->getOnMainInfo($data['id']);
            }

            $genres[] = $data;
        }

        if ($this->params['buildTree'] ?? false) {
            return $this->buildTree($genres, $this->params['id'] ?? '');
        }

        return $genres;
    }

    /**
     * @return string
     */
    public function getStringNameGenres(): string
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);
        $genres = [];

        foreach ($query->asArray()->all() as $item) {
            $genres[] = $item['name'];
        }

        return implode('^', $genres);
    }

    public function getGenreChildren(int $id, int $minLevel): array
    {
        $genresIdByParams = [];
        if (!empty($this->params)) {
            $genresIdByParams = $this->setParamsInQuery($this->entity::find());
            $genresIdByParams = $this->setOrderByInQuery($genresIdByParams)
                ->select('id')
                ->column();

            if (empty($genresIdByParams)) {
                return [];
            }
        }
        $query = $this->entity::find();
        $query = $this->setOrderByInQuery($query);
        $genres = [];

        foreach ($query->asArray()->all() as $item) {
            $genres[] = $this->convertField($item);
            if ($genres['onMain']) {
                $genres['onMainInfo'] = $this->getOnMainInfo($genres['id']);
            }
        }

        return $this->getChildren(
            $this->buildTree($genres, $id ?? 0),
            $minLevel,
            $genresIdByParams,
        );
    }

    public function create(): array
    {
        try {
            $this->entity->setScenario(self::SCENARIO_CREATE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();
            $this->entity->load($this->convertField($this->params, true), '');

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Создание не удалось');
            }

            if ($this->entity->is_course) {
                $this->saveRelatedFields($this->entity->id, $connection);
            }

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
            $this->entity = GenreEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Обновление не удалось');
            }
            // Жанры на главной
            if ($this->entity->is_course) {
                $this->saveRelatedFields($id, $connection);
            }

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
            $this->entity = GenreEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_DELETE);
            $connection = $this->entity::getDb();
            $transaction = GenreEntity::getDb()->beginTransaction();

            if (!$this->entity->delete()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Удаление не удалось');
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

    public function checkIsExist(): bool|int
    {
        $id = $this->setParamsInQuery($this->entity::find())
            ->select('id')
            ->scalar();

        if ($id) {
            return $id;
        }
        return false;
    }

    private function buildTree(array $list, int|string $parentId = ''): array
    {
        $output = [];

        foreach ($list as $item) {
            if ($item['parentId'] == $parentId) {
                $children = $this->buildTree($list, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $output[] = $item;
            }
        }

        return $output;
    }

    private function getChildren(array $list, int $minLevel, array $genresIdByParams): array
    {
        $output = [];
        foreach ($list as $item) {
            if (isset($item['children'])) {
                $output = array_merge($output, $this->getChildren($item['children'], $minLevel, $genresIdByParams));
            }
            unset($item['children']);
            if ($item['level'] >= ($minLevel ?? 0) && (!$genresIdByParams || in_array($item['id'], $genresIdByParams))) {
                $output[$item['id']] = $item;
            }
        }

        return $output;
    }

    private function saveRelatedFields(int $id, &$connection): void
    {
        if (isset($this->params['onMain']) && !$this->params['onMain']) {
            $this->deleteRelatedFields($id, $connection);
            return;
        }

        if (!$data = ($this->params['onMainInfo'] ?? [])) {
            return;
        }

        $products = [];
        if (!empty($data['products'])) {
            $repository = new ProductRepository();
            $repository->setParams(['withPagination' => false, 'id' => $data['products']]);
            $products = array_column($repository->getProducts()['products'], 'id');
        }

        $products = !empty($products) ? implode(',', $products) : '';
        $title = $data['title'] ?? '';
        $subtitle = $data['subtitle'] ?? '';
        $text = $data['text'] ?? '';
        $connection->createCommand("
            DELETE FROM genres_on_main WHERE genre_id = $id
        ")->execute();

        $connection->createCommand("
            INSERT INTO genres_on_main (genre_id, title, subtitle, products, `text`, date_created, date_updated) 
            VALUES (
                '{$id}',
                '{$title}',
                '{$subtitle}',
                '{$products}',
                '{$text}',
                current_date(),
                current_date()
            )
        ")->execute();
    }

    private function deleteRelatedFields(int $id, &$connection)
    {
        $connection->createCommand("
            DELETE FROM genres_on_main WHERE genre_id = $id
        ")->execute();
    }

    private function getOnMainInfo(int $id): array
    {
        try {
            $data = $this->entity::getDb()->createCommand("
                SELECT
                    title, subtitle, products, text
                FROM genres_on_main
                WHERE genre_id = {$id}
            ")->queryOne() ?: [];

            if (!empty($data)) {
                $data['products'] = explode(',', $data['products']);
                return $data;
            }
        } catch (\Exception) {}

        return [
            'title' => '',
            'subtitle' => '',
            'products' => [],
            'text' => '',
        ];
    }


    /**
     * @param int $id
     * @param bool $isCourse
     * @return array
     */
    public function getCourses(int $id, bool $isCourse = true): array
    {
        $query = $this->entity::find();
        $valueCourse = $isCourse ? 1 : 0;
        return $query->select("{$this->alias}id, {$this->alias}parent_id, {$this->alias}name")
            ->join('JOIN',
                'product_genres',
                "product_genres.product_id = {$id} AND product_genres.genre_id = {$this->alias}id")
            ->where("{$this->alias}is_course = {$valueCourse}")
            ->asArray()
            ->all();
    }

    public function getGenresList(array $ids): array
    {
        $ids = implode(',', $ids);
        return $this->entity::getDb()->createCommand("
            SELECT DISTINCT *
            FROM genres
            WHERE id IN (
                SELECT parent_id
                FROM genres
                WHERE id IN ($ids)
                ) OR id IN ($ids)
            ORDER BY id
        ")->queryAll();
    }

    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    public function getGenresByParentIds(array $ids): array
    {
        $genres = $this->getIdsByParentIds($ids);
        $genresVar = $genres;
        $list = [];
        while (!empty($genres)) {
            $genres = $this->getIdsByParentIds($genres);
            $list = array_unique(array_merge($genres, $list));
        }

        return array_unique(array_merge($genresVar, $list));
    }

    /**
     * @param array $genres
     * @return array
     * @throws Exception
     */
    public function getIdsByParentIds(array $genres): array
    {
        $stringIds = implode(',', $genres);
        return $this->entity::getDb()->createCommand("
                SELECT DISTINCT id 
                FROM genres 
                WHERE parent_id IN ({$stringIds}) AND id != 2483"
        )->queryColumn();
    }
}