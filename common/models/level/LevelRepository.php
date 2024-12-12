<?php

namespace common\models\level;

use common\models\AbstractRepository;


class LevelRepository extends AbstractRepository
{
    protected array $orders = ['id' => 'asc'];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];
    protected array $availableOrders = [
        'id',
    ];

    public function __construct()
    {
        $this->entity = new LevelEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'ProductId' => [
                'relativeField' => 'level',
            ],
        ]);
    }

    public function getLevels(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);
        $output = [];

        foreach ($query->asArray()->all() as $item) {
            $output[] = $this->convertField($item);
        }

        return $output;
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
            $this->entity = LevelEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Обновление не удалось');
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
}