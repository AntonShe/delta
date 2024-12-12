<?php

namespace common\models\api\tokens;

use common\models\AbstractRepository;
use yii\db\ActiveQuery;

class TokenRepository extends AbstractRepository
{
    public function __construct()
    {
        $this->entity = new TokenEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(),
            [
                'Type',
            ]
        );
    }

    public function getToken(): array
    {
        $query = $this->entity::find();
        $query = $this->setParamsInQuery($query);
        $query->andWhere("valid_till > now()");

        $items = $query
            ->asArray()
            ->one();

        return $items ?? [];
    }

    public function createToken(): bool
    {
        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $this->entity->load($this->params, '');

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function setTypeParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['type'])) {
            $query->andWhere(['type' => $this->params['type']]);
        }

        return $query;
    }
}