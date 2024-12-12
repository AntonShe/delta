<?php

namespace common\models\user_info;

use common\models\AbstractRepository;

class UserInfoRepository extends AbstractRepository
{
    protected array $fieldsMap = [
        'user_id' => 'userId',
    ];

    public function __construct()
    {
        $this->entity = new UserInfoEntity();

        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => [
                    'user_id', 'city'
                ]
            ],
        ]);
    }


    public function create(): bool
    {
        $this->entity->setScenario(self::SCENARIO_CREATE);
        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();
        try {
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
            $this->entity = UserInfoEntity::findOne($this->params['id']);
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
            var_dump($e->getMessage()); die();
            return false;
        }
    }

    /**
     * @return array|null
     */
    public function getUserInfo(): array|null
    {
        $query = $this->setParamsInQuery($this->entity::find());

        return $query
            ->asArray()->one();
    }
}