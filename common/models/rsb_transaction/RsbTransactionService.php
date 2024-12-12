<?php

namespace common\models\rsb_transaction;

use common\models\api\lpostApi\LpostClient;
use common\models\api\rsb\RSBClient;
use common\models\delivery_profile\DeliveryProfileRepository;
use common\models\logger\Logger;
use common\models\order\OrderRepository;

class RsbTransactionService
{
    protected RsbTransactionRepository $rsbTransactionRepository;
    protected RSBClient $rsbClient;
    protected LpostClient $deliveryApi;
    protected OrderRepository $orderRepository;
    protected DeliveryProfileRepository $profileRepository;

    public function __construct()
    {
        $this->rsbTransactionRepository = new RsbTransactionRepository();
        $this->rsbClient = new RSBClient();
        $this->deliveryApi = new LpostClient();
        $this->orderRepository = new OrderRepository();
        $this->profileRepository = new DeliveryProfileRepository();
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function closeBusinessDay(): void
    {
        $this->rsbClient->closeBusinessDay();
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function updateStatusTransactions(): void
    {
        $this->rsbTransactionRepository->setParams([
            'type' => $this->rsbTransactionRepository::TYPE_PAYMENT_PAID,
            'status' => [
                $this->rsbTransactionRepository::STATUS_CREATED,
                $this->rsbTransactionRepository::STATUS_PENDING
            ]
            ]);
        $transactions = $this->rsbTransactionRepository->getTransactions();

        if ($transactions) {
            $this->updateStatusPayment($transactions);
        }
    }

    /**
     * @param array $transIds
     * @return void
     * @throws \Exception
     */
    public function updateStatusPayment(array $transIds): void
    {
        foreach ($transIds as $value) {
            $status = $this->rsbClient->getStatusTransaction([
                'command' => 'c',
                'trans_id' => $value['transaction_id'],
                'client_ip_addr' => $value['client_ip_addr']
            ]);
            $intStatus = $this->getStatusPayment($status);

            if ($intStatus != $value['status']) {

                $this->rsbTransactionRepository->setParams(['status' => $intStatus]);
                $updateResult = $this->rsbTransactionRepository->update($value['id']);

                if ($status == 'OK' && $updateResult) {
                    $this->orderRepository->setParams(['orderNumber' => $value['order_number']]);
                    $order = $this->orderRepository->getOrders()['orders'][0];

                    $this->orderRepository->setParams([
                        'id' => $order['id'],
                        'status' => $this->orderRepository::ORDER_STATUS_PAYED
                    ]);
                    $this->orderRepository->updateOrderStatus();
                }
                Logger::getInstance()->writeLog(
                    'actionUpdateStatusPayment.log',
                    "Update transaction_id ". $value['transaction_id'] . " status result: " . $status,
                    true
                );
            }
        }

    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function updateBalanceAfterRefund(): bool
    {
        foreach ($this->deliveryApi->getFinancialInfo() as $balance) {
            if ($balance['Summa'] && $balance['ID_Order']) {

                $this->rsbTransactionRepository->setParams([
                    'orderNumber' => $balance['ID_Order'],
                    'type' => $this->rsbTransactionRepository::TYPE_PAYMENT_PAID,
                    'status' => $this->rsbTransactionRepository::STATUS_SUCCEEDED
                ]);
                $transInfo = $this->rsbTransactionRepository->getTransactions(isOne: true);

                $this->rsbTransactionRepository->setParams([
                    'orderNumber' => $balance['ID_Order'],
                    'type' => $this->rsbTransactionRepository::TYPE_PAYMENT_REFUND,
                    'status' => $this->rsbTransactionRepository::STATUS_SUCCEEDED
                ]);

                if ($refundTrans = $this->rsbTransactionRepository->getTransactions(isOne: true)) {
                    $result = $this->deliveryApi->updateFinancialInfo(
                        [
                            'ReturnSumma' => [
                                'ID_Prepay' => $balance['ID_Prepay'],
                                'Summa' => $refundTrans['amount']
                            ]
                        ]);

                    Logger::getInstance()->writeLog(
                        'actionUpdateBalanceAfterRefund.log',
                        "Result: " . json_encode($result),
                        true
                    );
                } elseif ($transInfo) {
                        $refundInfo = $this->rsbClient->refundPayment([
                            'command' => 'k',
                            'trans_id' => $transInfo['transaction_id'],
                            'amount' => floor($balance['Summa'] * 100)
                        ]);

                        if ($refundInfo['status'] == 'OK') {
                            $result = $this->deliveryApi->updateFinancialInfo(
                                [
                                    'ReturnSumma' => [
                                        'ID_Prepay' => $balance['ID_Prepay'],
                                        'Summa' => -$balance['Summa']
                                    ]
                                ]);

                            Logger::getInstance()->writeLog(
                                'actionUpdateBalanceAfterRefund.log',
                                "Result: " . json_encode($result),
                                true
                            );

                            $dataTrans = $this->getPreparedData(array_merge($transInfo, $refundInfo), isRefund: true);

                            $this->rsbTransactionRepository->setParams($dataTrans);
                            $this->rsbTransactionRepository->create();
                        }
                    }
                }
            }

        return true;
    }


    /**
     * @param array $data
     * @param bool $isRefund
     * @return array
     */
    public function getPreparedData(array $data, bool $isRefund = false): array
    {
        return [
            'transactionId' => $data['trans_id'],
            'orderNumber' => $data['order_number'],
            'amount' => $isRefund ? -$data['amount'] : $data['order_price'],
            'userId' => $data['user_id'],
            'uniqId' => $isRefund ? $data['uniq_id'] : $this->getUniqId($data['order_number']),
            'status' => $this->getStatusPayment($data['status']),
            'type' => $isRefund ? $this->rsbTransactionRepository::TYPE_PAYMENT_REFUND : $this->rsbTransactionRepository::TYPE_PAYMENT_PAID,
            'clientIpAddr' => $data['client_ip_addr']
            ];
    }

    /**
     * @param int $orderId
     * @return string
     */
    protected function getUniqId(int $orderId): string
    {
        return str_replace('.', '-', uniqid($orderId . '-', true));
    }

    /**
     * @param string $status
     * @return int
     */
    protected function getStatusPayment(string $status): int
    {
        return $this->rsbTransactionRepository->getStatus($status);
    }

    /**
     * @param int $orderNumber
     * @return void
     * @throws \Exception
     */
    public function refundTransaction(int $orderNumber): void
    {
        $this->rsbTransactionRepository->setParams([
            'type' => $this->rsbTransactionRepository::TYPE_PAYMENT_PAID,
            'orderNumber' => $orderNumber,
            'status' => $this->rsbTransactionRepository::STATUS_SUCCEEDED
        ]);
        $transInfo = $this->rsbTransactionRepository->getTransactions(isOne: true);

        if ($transInfo) {
            $refundInfo = $this->rsbClient->refundPayment([
                'command' => 'k',
                'trans_id' => $transInfo['transaction_id'],
                'amount' => floor($transInfo['amount'] * 100)
            ]);

            $dataTrans = $this->getPreparedData(array_merge($transInfo, $refundInfo), isRefund: true);

            $this->rsbTransactionRepository->setParams($dataTrans);
            $this->rsbTransactionRepository->create();

            foreach ($this->deliveryApi->getFinancialInfo() as $balance) {
                if ($balance['ID_Order'] == $transInfo['order_number']) {
                    if ($refundInfo['RESULT'] == "OK") {
                        $res = $this->deliveryApi->updateFinancialInfo(
                            [
                                'ReturnSumma' => [
                                    'ID_Prepay' => $balance['ID_Prepay'],
                                    'Summa' => -$balance['amount']
                                ]
                            ]);
                        Logger::getInstance()->writeLog(
                            'rejectOrderAndUpdateBalance.log',
                            "Result: " . json_encode($res),
                            true
                        );
                    }
                }
            }
        } else {
            throw new UnknownTransaction("Транзакция заказом $orderNumber не найдена");
        }
    }

    /**
     * @param array $order
     * @return void
     */
    public function createTransaction(array $order): void
    {
        $data = [
            'command' => 'v',
            'amount' => floor($order['order_price'] * 100),
            'currency' => '643',
            'client_ip_addr' => \Yii::$app->getRequest()->getUserIP(),
            'description' => "{$order['order_number']}",
            'language' => 'ru',
            'order_id' => "{$order['order_number']}"
        ];

        try {
            $transInfo = $this->rsbClient->getTransactionId($data);
            $transInfo['client_ip_addr'] = $data['client_ip_addr'];

            $dataTrans = $this->getPreparedData(array_merge($order, $transInfo));

            $this->rsbTransactionRepository->setParams($dataTrans);
            $this->rsbTransactionRepository->create();

        } catch (\Exception $e) {
            Logger::getInstance()->writeLog("TransactionError.log", $e->getMessage(), true);
        }
    }


    /**
     * @param string $transId
     * @return array
     * @throws \Exception
     */
    public function getOrderNumByTransId(string $transId): array
    {
        $this->rsbTransactionRepository->setParams(['transactionId' => $transId]);
        return $this->rsbTransactionRepository->getTransactions();
    }

    /**
     * @param string $transId
     * @return string
     */
    public function payment(string $transId, int $orderNumber): string
    {
        $this->rsbTransactionRepository->setParams(['transactionId' => $transId]);
        $transInfo = $this->rsbTransactionRepository->getTransactions(isOne: true);

        if ($transInfo) {

            if ($transInfo['status'] === $this->rsbTransactionRepository::STATUS_CANCELED) {

                $data = [
                    'order_price' => $transInfo['amount'],
                    'order_number' => $transInfo['order_number'],
                    'user_id' => $transInfo['user_id']
                ];

                $this->createTransaction($data);

                $this->rsbTransactionRepository->setParams([
                    'orderNumber' => $transInfo['order_number'],
                    'status' => $this->rsbTransactionRepository::STATUS_CREATED
                ]);
                $transInfo = $this->rsbTransactionRepository->getTransactions(isOne: true);
            }
        } else {
            $this->orderRepository->setParams(['orderNumber' => $orderNumber]);
            $order = $this->orderRepository->getOrders()['orders'][0];

            $this->profileRepository->setParams(['id' => $order['delivery_profile_id']]);
            $profile = $this->profileRepository->getProfile()[0];

            if (empty($deliveryPrice = $profile['price'])) {
                $rawCoords = json_decode($profile['coordinates'], true);

                $deliveryPrice = $this->deliveryApi->calculate([
                    'idPoint' => $profile['pointId'],
                    'latitude' => $rawCoords[0],
                    'longitude' => $rawCoords[1],
                ])['JSON_TXT'][0]['SumCost'];
            }

            $data = [
                'order_price' => $order['order_price'] + $deliveryPrice,
                'order_number' => $orderNumber,
                'user_id' => $order['user_id']
            ];

            $this->createTransaction($data);

            $this->rsbTransactionRepository->setParams([
                'orderNumber' => $orderNumber,
                'status' => $this->rsbTransactionRepository::STATUS_CREATED
            ]);
            $transInfo = $this->rsbTransactionRepository->getTransactions(isOne: true);
        }

        return $this->rsbClient->getRedirectUrl() . '?trans_id=' . $transInfo['transaction_id'];
    }

    public function rejectOrderByShop(): void
    {
        $this->rsbTransactionRepository->setParams([
            'status' => $this->rsbTransactionRepository::STATUS_CANCELED,
            'type' => $this->rsbTransactionRepository::TYPE_PAYMENT_PAID,
            'dateCreatedRange' => true
        ]);

        foreach ($this->rsbTransactionRepository->getTransactions() as $transInfo) {

            $this->rsbTransactionRepository->setParams([
                'exclude' => ['status' => $this->rsbTransactionRepository::STATUS_CANCELED],
                'orderNumber' => $transInfo['order_number']
            ]);
            $transInfoNotCanceled = $this->rsbTransactionRepository->getTransactions(isOne: true);

            if(!$transInfoNotCanceled && (time() > strtotime($transInfo['date_created'] . '+1 days'))) {

                $this->orderRepository->setParams(['orderNumber' => $transInfo['order_number']]);
                $orders = $this->orderRepository->getOrders()['orders'][0];

                $this->orderRepository->setParams([
                    'status' => $this->orderRepository::ORDER_STATUS_REJECT_BY_SHOP,
                    'id' => $orders['id'],
                    'date_storage' => null
                ]);

                $this->orderRepository->updateOrderStatus();
            }
        }
    }
}