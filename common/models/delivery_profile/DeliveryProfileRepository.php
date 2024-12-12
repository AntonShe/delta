<?php

namespace common\models\delivery_profile;

use common\models\AbstractRepository;
use yii\db\ActiveQuery;

class DeliveryProfileRepository extends AbstractRepository
{
    public const DELIVERY_TYPES = [
        'courier' => 1,
        'point' => 2,
    ];

    public function __construct()
    {
        $this->entity = new DeliveryProfileEntity();
        parent::__construct();
    }

    protected array $fieldsMap = [
        'user_id' => 'userId',
        'point_id' => 'pointId',
        'entry_code' => 'entryCode',
        'user_token' => 'userToken',
    ];

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(
            parent::getSettersParamsInQuery(),
            [
                'UserId',
                'UserToken'
            ]
        );
    }

    public function getProfile(?int $onlyLast = null): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        if (!is_null($onlyLast) && in_array($onlyLast, self::DELIVERY_TYPES)) {
            $query->andWhere(['type' => $onlyLast])
                ->limit(1)
                ->orderBy(['id' => SORT_DESC]);
        }

        $profiles = $query->asArray()->all();

        $result = [];

        foreach ($profiles as $profile) {
            $result[] = $this->convertField($profile);
        }

        return $result;
    }

    public function createProfile(): array
    {
        if (!empty($this->params['latitude']) && !empty($this->params['longitude'])) {
            $this->params['coordinates'] = json_encode([
                $this->params['latitude'],
                $this->params['longitude']
            ]);
        } else {
            return [];
        }

        $this->params['comment'] = $this->params['courierComment'] ?? '';

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $this->entity->load($this->convertField($this->params, true), '');

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return $this->entity->toArray();
            } else {
                $transaction->rollBack();

                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {

            return [];
        }
    }

    protected function setUserIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['userId'])) {
            $query->andWhere([$this->alias . 'user_id' => $this->params['userId']]);
        }

        return $query;
    }

    protected function setUserTokenParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['userToken'])) {
            $query->andWhere([$this->alias . 'user_token' => $this->params['userToken']]);
        }

        return $query;
    }
}