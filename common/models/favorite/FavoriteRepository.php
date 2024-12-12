<?php

namespace common\models\favorite;

use common\models\AbstractRepository;
use common\models\favorite\FavoriteEntity;
use yii\db\ActiveQuery;

class FavoriteRepository extends AbstractRepository
{
    protected array $fieldsMap = [
        'user_id' => 'userId',
        'session_key' => 'sessionKey',
        'product_id' => 'productId',
        'date_update' => 'dateUpdate',
    ];

    public function __construct()
    {
        $this->entity = new FavoriteEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(),
            [
                'ForUser',
                'Product'
            ]
        );
    }

    public function getFavorite(): array
    {
        $query = $this->setParamsInQuery($this->entity::find()->select('product_id'));

        $itemsList = $query
            ->indexBy('product_id')
            ->asArray()
            ->all();

        return array_keys($itemsList) ?? [];
    }

    public function deleteFavorite(): bool
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $item = $query->one();

        if (empty($item)) return false;

        $connection = $item::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($item->delete()) {
                $transaction->commit();

                return true;
            } else  {
                throw new \Exception(implode("\n", $item->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }


        return false;
    }

    public function addFavorite(): bool
    {
        $this->entity->load($this->convertField($this->params, true), '');
        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($this->entity->validate() && $this->entity-> save()) {
                $transaction->commit();

                return true;
            } else {
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }

        return false;
    }

    protected function setForUserParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['userId'])) {
            $query->andWhere([$this->alias . 'user_id' => $this->params['userId']]);
        } else if (isset($this->params['sessionKey'])) {
            $query->andWhere([$this->alias . 'session_key' => $this->params['sessionKey']]);
        }

        return $query;
    }

    protected function setProductParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['productId'])) {
            $query->andWhere([$this->alias . 'product_id' => $this->params['productId']]);
        }

        return $query;
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        try {
            $favourite = $this->entity::findOne($this->params['id']);

            if (is_null($favourite)) throw new \Exception('Не удалось найти отложенные!');

            $connection = $favourite::getDb();
            $transaction = $connection->beginTransaction();
            $this->params['dateUpdate'] = date('Y-m-d H:i:s', time());
            $favourite->attributes = $this->convertField($this->params, true);

            if ($favourite->validate() && $favourite->save()) {
                $transaction->commit();

                return true;
            }

            throw new \Exception(implode("\n", $favourite->getErrors()));
        } catch (\Exception $e) {
            var_dump($e); die($e->getMessage());
            return false;
        }
    }

    /**
     * @return array
     */
    public function getFavoriteFull(): array
    {
        $query = $this->setParamsInQuery($this->entity::find()->select('*'));

        return $query
            ->asArray()
            ->all();
    }
}