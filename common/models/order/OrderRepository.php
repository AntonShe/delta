<?php

namespace common\models\order;

use common\models\AbstractRepository;
use common\models\delivery_profile\DeliveryProfileRepository;
use common\models\logger\Logger;
use common\models\order_item\OrderItemRepository;
use common\models\Pagination;
use common\models\points\PointRepository;
use common\models\product\ProductRepository;
use common\models\product\ProductService;
use common\models\rsb_transaction\RsbTransactionRepository;
use common\models\user\UserRepository;
use yii\db\ActiveQuery;
use yii\db\Exception;

class OrderRepository extends AbstractRepository
{
//    const ORDER_STATUSES = [
//        'new' => 0,//Новый, ожидает оплаты или ручного перевода
//        'verified' => 1,//Создан (если оплата при получении) - ждет отправки в бд Logist
//        'payed' => 2,//Оплачен - подтвержден
//        'created' => 3,//Создан - отправлен в бд Logist
//        'reject' => 6, //Отменен
//    ];


    const ORDER_STATUS_NEW = 0; //Новый, ожидает оплаты или ручного перевода
    const ORDER_STATUS_VERIFIED = 1; //Создан (если оплата при получении) - ждет отправки в бд Logist
    const ORDER_STATUS_PAYED = 2; //Оплачен - подтвержден
    const ORDER_STATUS_CREATED = 31; //Создан - отправлен в бд Logist
    const ORDER_STATUS_ASSEMBLY = 32; //Сборка
    const ORDER_STATUS_SHIPPED = 33; //Отгружен, в пути
    const ORDER_STATUS_ACCEPTED_IN_THE_COURIER = 34; //Принят в курьерской службе
    const ORDER_STATUS_AVAILABLE_FOR_ISSUANCE = 36; //Доступен к выдаче ПВЗ
    const ORDER_STATUS_COMPLETED = 7; //Выполнен
    const ORDER_STATUS_REJECT = 8; //Отменен
    const ORDER_STATUS_REJECT_BY_SHOP = 9; //Отменен магазином
    const ORDER_STATUS_ERROR = 100; //При отправке в Logist произошла ошибка

    const PAYMENT_TYPE_CARD = 1; // Оплата картой
    const PAYMENT_TYPE_OFFLINE = 2;// Оплата при получении

    const ORDER_STATUSES = [
        self::ORDER_STATUS_NEW,
        self::ORDER_STATUS_VERIFIED,
        self::ORDER_STATUS_PAYED,
        self::ORDER_STATUS_CREATED,
        self::ORDER_STATUS_ASSEMBLY,
        self::ORDER_STATUS_SHIPPED,
        self::ORDER_STATUS_ACCEPTED_IN_THE_COURIER,
        self::ORDER_STATUS_AVAILABLE_FOR_ISSUANCE,
        self::ORDER_STATUS_COMPLETED,
        self::ORDER_STATUS_REJECT,
        self::ORDER_STATUS_REJECT_BY_SHOP,
        self::ORDER_STATUS_ERROR,
    ];

    protected DeliveryProfileRepository $profileRepository;
    protected ProductService $productService;
    protected OrderItemRepository $itemRepository;
    protected UserRepository $userRepository;
    protected PointRepository $pointRepository;
    protected Pagination $pagination;
    protected RsbTransactionRepository $rsbTransactionRepository;

    public function __construct()
    {
        $this->entity = new OrderEntity();
        $this->profileRepository = new DeliveryProfileRepository();
        $this->itemRepository = new OrderItemRepository();
        $this->userRepository = new UserRepository();
        $this->productService = new ProductService();
        $this->pointRepository = new PointRepository();
        $this->pagination = Pagination::getInstance();
        $this->rsbTransactionRepository = new RsbTransactionRepository();

        parent::__construct();
    }

    protected array $fieldsMap = [
        'session_key' => 'sessionKey',
        'manager_comment' => 'managerComment',
        'order_number' => 'orderNumber',
        'user_id' => 'userId',
        'delivery_profile_id' => 'deliveryProfileId',
        'payment_type' => 'paymentType',
        'status' => 'status',
        'manager_id' => 'managerId',
        'status_payment' => 'statusPayment',
        'order_price' => 'orderPrice',
        'getter_phone' => 'getterPhone',
        'getter_name' => 'getterName',
        'date_update' => 'dateUpdate',
        'delivery_date' => 'deliveryDate',
        'possible_delivery_date' => 'possibleDeliveryDate',
    ];

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(
            parent::getSettersParamsInQuery(),
            [
                'IdUser',
                'OrderNumber',
                'DateCreateRange',
                'Search',
                'Status',
                'Statuses',
                'NeedSend'
            ]
        );
    }

    public function getOrders(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
        }

        $orders = $query
            ->asArray()
            ->orderBy(['order_number' => SORT_DESC])
            ->all();

        return [
            'orders' => $orders,
            'pagination' => $this->pagination->getData()
        ];
    }

    public function createOrder(bool $needId = false): bool|int
    {
        $this->profileRepository->setParams(
            array_merge(
                $this->params['delivery'],
                [
                    'userId' => $this->params['user']['id']
                ]
            )
        );
        $newProfile = $this->profileRepository->createProfile();

        if (empty($newProfile)) return false;

        $orderData = $this->prepareDataBeforeSave($newProfile['id']);

        if (empty($orderData)) return false;

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $orderId = $connection->createCommand("SELECT id FROM orders WHERE order_number = {$orderData['order_number']}")->queryColumn();

            while (!empty($orderId)) {
                $orderData['order_number']--;
                $orderId = $connection->createCommand("SELECT id FROM orders WHERE order_number = {$orderData['order_number']}")->queryColumn();
            }

            $this->entity->load($this->convertField($orderData, true), '');

            if ($this->entity->validate() && $this->entity->save()) {
                $this->itemRepository->setParams([
                    'products' => $this->params['products'],
                    'orderId' => $this->entity->id
                ]);

                if ($this->itemRepository->createItems()) {
                    $transaction->commit();

                    return $needId ? $this->entity->id : true;
                }

                $transaction->rollBack();

                throw new \Exception('Не удалось записать товары.');
            } else {
                $transaction->rollBack();

                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }

        return false;
    }

    public function updateOrder(): bool
    {
        $order = $this->entity::findOne($this->params['id']);

        $connection = $order::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $this->itemRepository->setParams(['idOrder' => $this->params['id']]);

            if (!$this->itemRepository->deleteItems()) {
                $transaction->rollBack();

                return false;
            }

            $this->profileRepository->setParams(
                array_merge(
                    $this->params['delivery'],
                    [
                        'userId' => $this->params['user']['id']
                    ]
                )
            );
            $newProfile = $this->profileRepository->createProfile();

            if (empty($newProfile)) {
                $transaction->rollBack();

                return false;
            }

            $orderData = $this->prepareDataBeforeSave($newProfile['id']);

            if ($order['status'] !== self::ORDER_STATUS_ERROR) {
                $orderData['status'] = $order['status'];
            } else {
                $this->rsbTransactionRepository->setParams([
                    'orderNumber' => $order['order_number'],
                    'type' => $this->rsbTransactionRepository::TYPE_PAYMENT_PAID,
                    'status' => $this->rsbTransactionRepository::STATUS_SUCCEEDED
                    ]);
                if ($this->rsbTransactionRepository->getTransactions(isOne: true)) {
                    $orderData['status'] = self::ORDER_STATUS_PAYED;
                }

            }

            if (empty($orderData)) {
                $transaction->rollBack();

                return false;
            }
            $orderData['orderNumber'] = $order->order_number;

            $order->load($this->convertField($orderData, true), '');

            if ($order->validate() && $order->save()) {
                $this->itemRepository->setParams([
                    'products' => $this->params['products'],
                    'orderId' => $order->id
                ]);

                if ($this->itemRepository->createItems()) {
                    $transaction->commit();

                    return true;
                }

                $transaction->rollBack();

                throw new \Exception('Не удалось записать товары.');
            } else {
                $transaction->rollBack();

                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }

        return false;
    }

    protected function prepareDataBeforeSave(int $deliveryProfileId): array
    {
        $price = 0;
        $isAdmin = \Yii::$app->user->isAdmin();

        foreach ($this->params['products'] as $product) {
            if ($product['quantityCart'] > $product['quantity']) return [];

            $price += $product['quantityCart'] * $product['price'];
        }

        $data = [
            'order_number' => time() - 993993994,
            'userId' => $this->params['user']['id'],
            'sessionKey' => $this->params['user']['sessionKey'],
            'deliveryProfileId' => $deliveryProfileId,
            'paymentType' => $this->params['paymentType'],
            'orderPrice' => $price,
            'status' => $this->params['paymentType'] === self::PAYMENT_TYPE_OFFLINE ? self::ORDER_STATUS_VERIFIED : self::ORDER_STATUS_NEW,
            'statusPayment' => 0,
            'deliveryDate' => $this->params['deliveryDate'],
            'possibleDeliveryDate' => $this->params['deliveryDate'],
        ];

        if (!empty($this->params['managerComment'])) $data['managerComment'] = $this->params['managerComment'];

        $data['getterPhone'] = (!empty($this->params['subUser']['subPhone']))
            ? $this->params['subUser']['subPhone']
            : $this->params['user']['phone'];

        $data['getterName'] = (
            !empty($this->params['subUser']['subFirstName'])
            && !empty($this->params['subUser']['subLastName'])
        )
            ? $this->params['subUser']['subLastName'].' '.$this->params['subUser']['subFirstName']
            : $this->params['user']['lastName'].' '.$this->params['user']['firstName'];

        if ($isAdmin) $data['managerId'] = \Yii::$app->user->getId();

        return $data;
    }

    public function updateOrderStatus(): bool
    {

        if (empty($this->params['id']) || !in_array($this->params['status'], self::ORDER_STATUSES)) return false;

        $order = $this->entity::findOne($this->params['id']);

        if (empty($order)) return false;

        $connection = $order::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $order->status = $this->params['status'];
            $order->date_storage = $this->params['date_storage'];
            $order->delivery_date = $this->params['delivery_date'];

            if ($order->validate() && $order->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();

                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Throwable $e) {
            Logger::getInstance()->writeLog(
                'actionSendOrder.log',
                "Update order ".$this->params['id']." status error: " . $e->getMessage()
            );

            return false;
        }

        return false;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getOrderFull(): array
    {
        $order = $this->convertField($this->entity::findOne($this->params['id'])->toArray());
        $this->userRepository->setParams(['id' => $order['userId']]);
        $user = $this->userRepository->getUsers()['users'][0];

        $this->profileRepository->setParams(['id' => $order['deliveryProfileId']]);
        $profile = $this->profileRepository->getProfile()[0];


        if ($profile['type'] == 1) {
            $city = explode(', ', $profile['address']);
            $coords = json_decode($profile['coordinates'], true);
            // TODO Этот костыль надо будет починить на этапе получения DeliveryKind

            $profile['city'] = match ($city[0]) {
                'Московская область' => 'Москва',
                'Ленинградская область' => 'Санкт-Петербург',
                'Сахалинская область' => $city[1],
                default => $city[0]
            };

            $profile['courierComment'] = $profile['comment'] ?? '';
            $profile['latitude'] = $coords[0];
            $profile['longitude'] = $coords[1];
        } else if ($profile['type'] == 2) {
            $this->pointRepository->setParams(['idPoint' => $profile['pointId']]);
            $point = $this->pointRepository->getPoints()[0];
            $profile['city'] = $point['cityName'];
            $profile['latitude'] = $point['latitude'];
            $profile['longitude'] = $point['longitude'];
        }

        $subUser = [];
        $fio = explode(' ', $order['getterName']);

        if ($user['phone'] != $order['getterPhone']) $subUser['subPhone'] = $order['getterPhone'];
        if ($user['lastName'] != $fio[0]) $subUser['subLastName'] = $fio[0];
        if ($user['firstName'] != $fio[1]) $subUser['subFirstName'] = $fio[1];

        $this->itemRepository->setParams(['idOrder' => $this->params['id']]);
        $items = $this->itemRepository->getItems();
        $products = [];

        foreach ($items as $item) {
            $this->productService->setParams(['id' => $item['productId']]);
            $product = $this->productService->getProducts()['products'][0];
            $product['priceInOrder'] = $item['productPrice'];
            $product['quantityCart'] = $item['quantity'];
            $products[] = $product;
        }

        return [
            'status' => $order,
            'user' => $user,
            'products' => $products,
            'delivery' => $profile,
            'paymentType' => $order['paymentType'],
            'managerComment' => $order['managerComment'],
            'subUser' => $subUser,
            'storageDate' => $order['date_storage'] ? date('d.m.Y', strtotime($order['date_storage'])) : '',
            'transaction' => $this->getTransInfo($order)
        ];
    }

    protected function setIdUserParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['idUser'])) {
            $query->andWhere(['user_id' => $this->params['idUser']]);
        }

        return $query;
    }

    protected function setOrderNumberParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['orderNumber'])) {
            $query->andWhere("CAST(order_number as CHAR) LIKE '%{$this->params['orderNumber']}%'");
        }

        return $query;
    }

    protected function setSearchParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['search'])) {
            $query->andWhere("id like '%{$this->params['search']}%' or order_number like '%{$this->params['search']}%'");
        }

        return $query;
    }

    protected function setStatusParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['status'])) {
            $query->andWhere(['status' => $this->params['status']]);
        }

        return $query;
    }

    protected function setStatusesParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['statuses']) && is_array($this->params['statuses']) && !empty($this->params['statuses'])) {
            $statusList = implode(', ', $this->params['statuses']);
            $query->andWhere("status in ({$statusList})");
        }

        return $query;
    }

    protected function setDateCreateRangeParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['dateCreateRange'])) {
            $dates = json_decode($this->params['dateCreateRange'], true);
            $dateStart = date('Y-m-d', strtotime($dates[0])) . ' 00:00:00';
            $dateEnd = date('Y-m-d', strtotime($dates[1])) . ' 23:59:59';

            $query->andWhere("date_create >= '{$dateStart}' and date_create <= '{$dateEnd}'");
        }

        return $query;
    }

    protected function setNeedSendParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['needSend']) && $this->params['needSend']) {
            $query->andWhere([
                'OR',
                'status = ' . self::ORDER_STATUS_VERIFIED,
                'status = ' . self::ORDER_STATUS_PAYED . ' AND order_price <= 70000',
            ])
                ->andWhere('status != ' . self::ORDER_STATUS_ERROR);
        }

        return $query;
    }

    /**
     * @param array $order
     * @return array
     */
    protected function getTransInfo(array $order): array
    {
        $output = [];
        if ($order['paymentType'] == self::PAYMENT_TYPE_CARD) {
            $this->rsbTransactionRepository->setParams([
                'orderNumber' => $order['orderNumber']
            ]);
            $this->rsbTransactionRepository->setOrder(['id' => 'DESC']);
            $transInfo = $this->rsbTransactionRepository->getTransactions(isOne: true);
            if ($transInfo) {
                $output['isPending'] = $transInfo['status'] == $this->rsbTransactionRepository::STATUS_PENDING;
                $output['trans_id'] = $transInfo['transaction_id'];
            }
        }

        return $output;
    }
}