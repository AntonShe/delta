<?php

namespace common\models\cart_item;

use common\models\AbstractRepository;
use common\models\product\ProductService;
use yii\db\ActiveQuery;

class CartItemRepository extends AbstractRepository
{
    protected ProductService $productService;

    protected array $fieldsMap = [
        'cart_id' => 'cartId',
        'product_id' => 'productId',
        'default_price' => 'defaultPrice',
        'final_price' => 'finalPrice'
    ];

    public function __construct()
    {
        $this->entity = new CartItemEntity();
        $this->productService = new ProductService();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(),
            [
                'CartId',
            ]
        );
    }

    protected function setCartIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (!empty($this->params['cartId'])) {
            $query->where(['cart_id' => $this->params['cartId']]);
        }

        return $query;
    }

    public function getItems(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        $items = $query
            ->indexBy('product_id')
            ->asArray()
            ->all();

        return $items;
    }

    public function createItem(): bool
    {
        $this->entity->setScenario(self::SCENARIO_CREATE);
        $this->productService->setParams(['id' => $this->params['productId']]);
        $product = $this->productService->getProducts()['products'];

        if (empty($product)) return false;

        $this->mergeParams([
            'defaultPrice' => $product[0]['oldPrice'],
            'finalPrice' => $product[0]['price'],
        ]);

        $this->entity->load($this->convertField($this->params, true), '');

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $errors = implode("\n", $this->entity->getErrors());
                throw new  \yii\base\Exception($errors);
            }
        }  catch (\Exception $e) {
            return false;
        }
    }

    public function updateItem(): bool
    {
        $this->entity->setScenario(self::SCENARIO_UPDATE);
        $item = $this->entity::findOne($this->params['id']);

        if (is_null($item)) return false;

        $connection = $item::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $item->load($this->convertField($this->params), '');

            if ($item->validate() && $item->save()) {
                $transaction->commit();

                return true;
            } else {
                throw new \Exception(implode("\n", $item->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }
    }

    public function deleteItem(): bool
    {
        $query = $this->setParamsInQuery($this->entity::find());

        $item = $query
            ->asArray()
            ->one();

        if (empty($item)) return true;

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $delItem = $this->entity::findOne($item['id']);

            if (!$delItem->delete()) throw new \Exception(implode("\n", $delItem->getErrors()));

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }
    }

    public function deleteItems(): bool
    {
        $query = $this->setParamsInQuery($this->entity::find());

        $items = $query
            ->asArray()
            ->all();

        if (empty($items)) return true;

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            foreach ($items as $item) {
                $delItem = $this->entity::findOne($item['id']);

                if (!$delItem->delete()) throw new \Exception(implode("\n", $delItem->getErrors()));
            }

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }
    }
}