<?php

namespace common\models\order_item;

use common\models\AbstractRepository;
use yii\db\ActiveQuery;

class OrderItemRepository extends AbstractRepository
{
    public function __construct()
    {
        $this->entity = new OrderItemEntity();

        parent::__construct();
    }

    protected array $fieldsMap = [
        'order_id' => 'orderId',
        'product_id' => 'productId',
        'product_price' => 'productPrice',
    ];

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(
            parent::getSettersParamsInQuery(),
            [
                'OrderId',
                'IdProduct'
            ]
        );
    }

    public function createItems(): bool
    {
        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            foreach ($this->params['products'] as $item) {
                $newItem = new OrderItemEntity();

                $newItem->load($this->convertField([
                    'orderId' => $this->params['orderId'],
                    'productId' => $item['id'],
                    'productPrice' => $item['price'],
                    'quantity' => $item['quantityCart'],
                ], true), '');

                if ($newItem->validate() && $newItem->save()) {
                    continue;
                } else {
                    var_dump($newItem->getErrors());
                }


                $transaction->rollBack();

                return false;
            }

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteItems(): bool
    {
        $items = $this->getItems();

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            foreach ($items as $item) {
                $loadedItem = $this->entity::findOne($item['id']);

                if (!$loadedItem->delete()) {
                    $transaction->rollBack();

                    return false;
                }
            }

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }
    }

    public function getItems() {
        $query = $this->setParamsInQuery($this->entity::find());
        $items = $query->asArray()->all();

        $result = [];

        foreach ($items as $item) {
            $result[] = $this->convertField($item);
        }

        return $result;
    }

    protected function setOrderIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['idOrder'])) {
            $query->where(['order_id' => $this->params['idOrder']]);
        }

        return $query;
    }

    protected function setIdProductParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['idProduct'])) {
            $query->where(['product_id' => $this->params['idProduct']]);
        }

        return $query;
    }
}