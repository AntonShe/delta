<?php

namespace common\models\series;

use common\models\AbstractRepository;

class SeriesRepository extends AbstractRepository
{
    protected array $fieldsMap = [
        'labirint_id' => 'labirintId',
        'publishing_house_id' => 'publishingHouseId',
    ];

    public function __construct()
    {
        $this->entity = new SeriesEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => [
                    'id', 'labirint_id', 'publishing_house_id'
                ]
            ]
        ]);
    }

    /**
     * @return array
     */
    public function getSeries(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);
        $output = [];

        foreach ($query->asArray()->all() as $item) {
            $output[] = $this->convertField($item);
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        try {
            $this->entity->setScenario(self::SCENARIO_CREATE);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            $this->entity->load($this->convertField($this->params, true), '');

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }

    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        try {
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $this->entity = SeriesEntity::findOne($this->params['id']);
            $connection = $this->entity::getDb();

            $transaction = $connection->beginTransaction();

            $this->entity->setAttributes($this->convertField($this->params, true));

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }

        } catch (\Exception $e) {
            var_dump($e->getMessage());die();
            return false;
        }
    }

    /**
     * @return bool|int
     */
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