<?php

namespace common\models\cart;

use common\models\AbstractRepository;
use common\models\cart_item\CartItemRepository;
use common\models\Pagination;
use yii\db\ActiveQuery;
use yii\db\Exception;

class CartRepository extends AbstractRepository
{
    protected CartItemRepository $itemRepository;

    protected array $fieldsMap = [
        'user_id' => 'userId',
        'session_key' => 'sessionKey',
        'raw_price' => 'rawPrice',
        'final_price' => 'finalPrice',
        'discount_sum' => 'discountSum',
        'date_update' => 'dateUpdate',
    ];

    public function __construct()
    {
        $this->entity = new CartEntity();
        $this->itemRepository = new CartItemRepository();

        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'SessionKey',
            'UserId'
        ]);
    }

    public function getCart(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        $cart = $query
            ->asArray()
            ->one();

        if (empty($cart)) return [];

        $this->itemRepository->setParams(['cartId' => $cart['id']]);
        $cart['items'] = $this->itemRepository->getItems();

        return $cart;
    }

    public function createCart(): array
    {
        $this->entity->load($this->convertField($this->params, true), '');

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();
                $cart = $this->entity->toArray();
                $cart['items'] = [];

                return $cart;
            } else {
                $errors = implode("\n", $this->entity->getErrors());
                throw new  \yii\base\Exception($errors);
            }
        }  catch (\Exception $e) {
            return [];
        }
    }

    public function addToCart(int $idCart): bool
    {
        $this->params['cartId'] = $idCart;

        $this->itemRepository->setParams($this->params);

        return $this->itemRepository->createItem();
    }

    public function updateCart(): bool
    {
        try {
            $cart = $this->entity::findOne($this->params['id']);

            if (is_null($cart)) throw new \Exception('Не удалось найти корзину!');

            $connection = $cart::getDb();
            $transaction = $connection->beginTransaction();
            $this->params['dateUpdate'] = date('Y-m-d H:i:s', time());
            $cart->attributes = $this->convertField($this->params, true);

            if ($cart->validate() && $cart->save()) {
                $transaction->commit();

                return true;
            }

            throw new \Exception(implode("\n", $cart->getErrors()));
        }catch (\Exception $e) {
            var_dump($e); die($e->getMessage());
            return false;
        }

    }

    public function setQuantity(): bool
    {
        $this->itemRepository->setParams($this->params);

        if ($this->params['quantity'] == 0) {
            return $this->itemRepository->deleteItem();
        }

        $this->clearParams();

        return $this->itemRepository->updateItem();
    }

    public function deleteItems(): bool
    {
        $this->itemRepository->setParams($this->params);
        $this->clearParams();

        return $this->itemRepository->deleteItems();
    }

    protected function setSessionKeyParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['sessionKey'])) {
            $query->andWhere(['session_key' => $this->params['sessionKey']]);
        }

        return $query;
    }

    protected function setUserIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['userId'])) {
            $query->andWhere(['user_id' => $this->params['userId']]);
        }

        return $query;
    }

    public function deleteCart(): bool
    {
        if (empty($this->params['id'])) return false;

        $cart = $this->entity::findOne($this->params['id']);

        $connection = $cart::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $this->itemRepository->setParams([
                'cartId' => $this->params['id']
            ]);

            if ($this->itemRepository->deleteItems() && $cart->delete()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();

                throw new \Exception(implode("\n", $cart->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }

        return false;
    }
}