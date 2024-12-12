<?php

namespace common\models\promotion;

use common\models\AbstractRepository;
use common\models\Pagination;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class PromotionRepository extends AbstractRepository
{
    const IMAGE_PATH = '/admin/img/promotions';

    protected Pagination $pagination;

    protected array $fieldsMap = [
        'tablet_image' => 'tabletImage',
        'mobile_image' => 'mobileImage',
        'start_date' => 'startDate',
        'end_date' => 'endDate',
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
        $this->entity = new PromotionEntity();
        $this->pagination = Pagination::getInstance();

        parent::__construct();
    }

    public function setParams(array $params): void
    {
        foreach (['image', 'tablet_image', 'mobile_image'] as $field) {
            if (isset($params["{$field}File"]) && $params["{$field}File"]) {
                $this->images[] = $field;
            }
        }

        $this->params = $params;
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'IsActive',
            'Simple' => [
                'params' => [
                    'id',
                ]
            ],
        ]);
    }

    public function read(): array
    {
        $output = [];
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
            $output['pagination'] = $this->pagination->getData();
        }
        
        $data = $query->asArray()->all();

        foreach ($data as &$banner) {
            $banner = $this->convertField($banner);
            $this->convertDate($banner);
        }

        $output['data'] = $data;

        return $output;
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

            $this->saveImages();

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
            $this->entity = PromotionEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \yii\db\Exception('Обновление не удалось');
            }

            // Связанные поля
            $this->saveImages();

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
            $this->entity = PromotionEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_DELETE);

            $this->entity->delete();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
        } catch (\Throwable $e) {
            $this->setErrors($e->getMessage());
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

    /**
     * @throws \Exception
     */
    private function saveImages(): void
    {
        if (empty($this->images)) {
            return;
        }
        try {
            BaseFileHelper::createDirectory('img/promotions');

            foreach ($this->images as $fieldName) {
                $this->entity->{"{$fieldName}File"} = UploadedFile::getInstance(
                    $this->entity,
                    "{$fieldName}File"
                );

                $fileName = "promotion-{$this->entity->id}-{$fieldName}-" . time() .".{$this->entity->{"{$fieldName}File"}->extension}";

                if (!$this->entity->{"{$fieldName}File"} || !$this->entity->upload($fieldName, $fileName)) {
                    unlink($this->entity->{"{$fieldName}File"}->tempName);
                    throw new \Exception('Ошибка при сохранении изображения');
                }
                $this->entity->setAttribute($fieldName, $fileName);
            }

            $this->entity->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function convertDate(array &$shelf): void
    {
        $shelf['startDate'] = date_format(new \DateTime($shelf['startDate']), 'Y-m-d');
        $shelf['endDate'] = date_format(new \DateTime($shelf['endDate']), 'Y-m-d');
    }
}