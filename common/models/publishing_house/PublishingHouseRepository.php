<?php

namespace common\models\publishing_house;

use common\models\AbstractRepository;
use common\models\Pagination;


class PublishingHouseRepository extends AbstractRepository
{
    protected Pagination $pagination;
    protected array $fieldsMap = [
        'labirint_id' => 'labirintId',
        'date_updated' => 'dateUpdated',
        'seo_title' => 'seoTitle',
        'seo_meta_keywords' => 'seoMetaKeywords',
        'seo_meta_description' => 'seoMetaDescription',
        'is_active' => 'isActive'
    ];

    protected array $hiddenFields = [
        'date_created' => 'dateCreated'
    ];
    protected array $availableOrders = [
        'name',
    ];

    public function __construct()
    {
        $this->entity = new PublishingHouseEntity();
        $this->pagination = Pagination::getInstance();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => [
                    'id', 'labirint_id'
                ]
            ],
            'PublisherSearch'
        ]);
    }

    public function getPublishingHouses(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
        }

        $publishers = $query->asArray()->all();

        foreach ($publishers as &$publisher) {
            $publisher = $this->convertField($publisher);
        }

        return [
            'publishers' => $publishers,
            'pagination' => $this->pagination->getData()
        ];
    }

    public function create(): array
    {
        try {
            $this->entity->setScenario(self::SCENARIO_CREATE);
            $this->entity->load($this->convertField($this->params, true), '');

            if (!$this->entity->validate()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Создание не удалось');
            }

            $this->entity->save();
        } catch (\Exception $e) {
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

    public function update(int $id): array
    {
        try {
            $this->entity = PublishingHouseEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Создание не удалось');
            }
            $this->entity->save();

        } catch (\Exception $e) {
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

    public function delete(int $id): array
    {
        $this->entity->setScenario(self::SCENARIO_DELETE);
        return [];
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

    /**
     * @return int
     */
    public function getLastId(): int
    {
        return $this->entity->id;
    }
}