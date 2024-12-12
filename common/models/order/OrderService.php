<?php

namespace common\models\order;

use common\models\api\lpostApi\LpostClient;
use common\models\api\rsb\RSBClient;
use common\models\cart\CartRepository;
use common\models\delivery_profile\DeliveryProfileRepository;
use common\models\logger\Logger;
use common\models\pdf_generator\SpecificationsPDFGenerator;
use common\models\pdf_generator\OrderBillPDFGenerator;
use common\models\product\ProductRepository;
use common\models\product\ProductService;
use common\models\rsb_transaction\RsbTransactionService;
use common\models\user\UserRepository;
use common\models\user\UserService;
use yii\db\Exception;

class OrderService
{
    const ERRORS = [
        'Ссылка не валидна.'
    ];

    protected OrderRepository $repository;
    protected CartRepository $cartRepository;
    protected ProductService $productService;
    protected UserRepository $userRepository;
    protected DeliveryProfileRepository $deliveryProfileRepository;
    protected LpostClient $deliveryApi;
    protected RSBClient $rsbClient;
    protected RsbTransactionService $rsbTransactionService;

    public function __construct()
    {
        $this->repository = new OrderRepository();
        $this->cartRepository = new CartRepository();
        $this->productService = new ProductService();
        $this->userRepository = new UserRepository();
        $this->deliveryProfileRepository = new DeliveryProfileRepository();
        $this->deliveryApi = new LpostClient();
        $this->rsbClient = new RSBClient();
        $this->rsbTransactionService = new RsbTransactionService();
    }

    public function setParams(array $data): void
    {
        $this->repository->setParams($data);
    }

    public function getOrders(): array
    {
        return $this->repository->getOrders();
    }

    public function createOrder(): bool
    {
        return $this->repository->createOrder();
    }

    /**
     * @param array $params
     * @return int
     */
    public function createOrderFromCart(array $params): int
    {
        if (empty($params['orderParams']['carId'])) return 0;

        $orderData = [
            'id' => 0,
            'subUser' => [
                'subPhone' => '',
                'subLastName' => '',
                'subFirstName' => '',
            ],
            'paymentType' => $params['orderParams']['paymentType'],
            'managerComment' => '',
        ];
        $deliveryDate = explode('T', $params['orderParams']['deliveryDate']);
        $orderData['deliveryDate'] = $deliveryDate[0];

        if (\Yii::$app->user->isGuest) {
            $isCreated = $this->createUserFromOrder($params['userData']);

            if ($isCreated) {
                $identifier = \Yii::$app->session->get(\Yii::$app->user::TOKEN_KEY);

                if (is_null($identifier)) {
                    $identifier = \Yii::$app->request->cookies->get(\Yii::$app->user::TOKEN_KEY);
                }

                if (!is_null($identifier)) {
                    $sessionKey = is_string($identifier) ? $identifier : $identifier->value;
                } else {
                    return 0;
                }

                $this->userRepository->setParams(['BySession' => [
                        'sessionKey' => $sessionKey,
                        'userType' => $this->userRepository::CUSTOMER_USER
                    ]
                ]);
                $user = $this->userRepository->getUsers()['users'][0];
                $this->userRepository->setIdentity($user['id']);
                \Yii::$app->user->login($this->userRepository, UserService::LOGIN_DURATION);
            } else {
                return 0;
            }
        } else {
            $this->userRepository->setParams(['id' => \Yii::$app->user->getId()]);
            $user = $this->userRepository->getUsers()['users'][0];
        }

        $orderData['user'] = $user;
        $this->cartRepository->setParams(['id' => [$params['orderParams']['carId']]]);
        $cart = $this->cartRepository->getCart();

        foreach ($cart['items'] as $item) {
            $this->productService->setParams(['id' => $item['product_id']]);
            $product = $this->productService->getProducts()['products'][0];
            $product['quantityCart'] = $item['quantity'];
            $orderData['products'][] = $product;
        }

        $this->deliveryProfileRepository->setParams(['id' => $params['orderParams']['deliveryProfileId']]);

        $profileData = $this->deliveryProfileRepository->getProfile()[0];
        $rawAddress = explode(', ', $profileData['address']);
        $rawCoords = json_decode($profileData['coordinates'], true);

        $deliveryData = $this->deliveryApi->calculate([
            'idPoint' => $profileData['pointId'],
            'latitude' => $rawCoords[0],
            'longitude' => $rawCoords[1],
        ])['JSON_TXT'][0];

        $orderData['delivery'] = [
            'city' => $rawAddress[0],
            'address' => $profileData['address'],
            'flat' => $profileData['flat'],
            'entry' => $profileData['entry'],
            'flor' => $profileData['flor'],
            'entryCode' => $profileData['entryCode'],
            'courierComment' => $params['orderParams']['courierComment'],
            'latitude' => $rawCoords[0],
            'longitude' => $rawCoords[1],
            'pointId' => $profileData['pointId'],
            'type' => $profileData['type'],
            'price' => $deliveryData['SumCost'],
        ];

        $this->setParams($orderData);

        $newOrderId = $this->repository->createOrder(true);

        if ($newOrderId === false) return 0;

        $this->cartRepository->setParams(['cartId' => $cart['id']]);
        $this->cartRepository->deleteItems();

        $this->repository->setParams(['id' => $newOrderId]);
        $order = $this->repository->getOrders()['orders'][0];

        if ($order['payment_type'] == $this->repository::PAYMENT_TYPE_CARD) {
            $order['order_price'] += $deliveryData['SumCost'];
            $this->rsbTransactionService->createTransaction($order);
        }

        return $order['order_number'];
    }

    public function updateOrder(): bool
    {
        return $this->repository->updateOrder();
    }

    public function getOrderFull(): array
    {
        return $this->repository->getOrderFull();
    }

    public function getSpecifications(array $params): string
    {
        $tokenData = $this->checkToken($params);

        if ($tokenData === false) return self::ERRORS[0];

        $this->setParams(['id' => $tokenData]);

        $generator = new SpecificationsPDFGenerator('Specifications.pdf');
        $generator->generate($this->getOrderFull());

        return $generator->getFileToView();
    }

    public function getOderBill(array $params): string
    {
        $tokenData = $this->checkToken($params);

        if ($tokenData === false) return self::ERRORS[0];

        $this->setParams(['id' => intval($tokenData)]);
        $generator = new OrderBillPDFGenerator('Счет на оплату заказа.pdf');
        $generator->generate($this->getOrderFull());

        return $generator->getFileToView();
    }

    private function checkToken(array $params): string|bool
    {
        if (empty($params['token'])) return false;

        $time = time();
        $tokenData = explode('o', $params['token']);

        if (count($tokenData) !== 2) return false;

        $timeTokenCreate = intval($tokenData[1]) / intval($tokenData[0]);
        $timeDelta = $time - $timeTokenCreate;

        if ($timeDelta > 432000) return false;

        return $tokenData[0];
    }

    protected function createUserFromOrder(array $params): bool
    {
        $identifier = \Yii::$app->session->get(\Yii::$app->user::TOKEN_KEY);

        if (is_null($identifier)) {
            $identifier = \Yii::$app->request->cookies->get(\Yii::$app->user::TOKEN_KEY);
        }

        if (!is_null($identifier)) {
            $params['sessionKey'] = is_string($identifier) ? $identifier : $identifier->value;;
        }

        $params['isLegal'] = 0;
        return $this->userRepository->createUser($params);
    }

    public function updateOrderStatus(): bool
    {
        return $this->repository->updateOrderStatus();
    }

    public function rejectOrder(): bool
    {
        if (\Yii::$app->user->isGuest) return false;

        $this->repository->mergeParams([
            'idUser' => \Yii::$app->user->getId()
        ]);

        return $this->reject();
    }

    public function rejectOrderFromAdmin(): bool
    {
        return $this->reject();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function reject(): bool
    {
        if (empty($orders = $this->repository->getOrders()['orders'])) return false;
        $this->repository->mergeParams([
            'status' => $this->repository::ORDER_STATUS_REJECT,
            'date_storage' => null
        ]);

        $result = $this->repository->updateOrderStatus();

        if ($result) {
            $data = [
                'Orders' => [
                    [
                        'ID_Order' => $orders[0]['order_number'],
                        'Reject' => 1
                    ]
                ]
            ];
            $this->deliveryApi->rejectOrder($data);

            if ($orders[0]['payment_type'] === $this->repository::PAYMENT_TYPE_CARD
                &&
                $orders[0]['status'] !== $this->repository::ORDER_STATUS_NEW
            ) {
                try {
                    $this->rsbTransactionService->refundTransaction((int)$orders[0]['order_number']);
                } catch (\Exception $exception) {
                    Logger::getInstance()->writeLog(
                        'rejectOrderAndUpdateBalance.log',
                        $exception->getMessage(),
                        true
                    );
                }

            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getPreparedOrders(): array
    {
        $orders = $this->getOrders();
        foreach ($orders['orders'] as &$order) {
            $this->deliveryProfileRepository->setParams(['id' => $order['delivery_profile_id']]);
            $order['order_price'] += (float)$this->deliveryProfileRepository->getProfile()[0]['price'];
        }

        return $orders;
    }
}