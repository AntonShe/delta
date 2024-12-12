<?php

namespace console\models;

use common\models\AbstractMailSender;
use common\models\api\lpostApi\LpostClient;
use common\models\logger\Logger;
use common\models\order\OrderRepository;
use common\models\order\OrderService;

class OrderSender
{
    private OrderService $service;
    private LpostClient $apiClient;
    private AbstractMailSender $mailSender;

    public function __construct()
    {
        $this->service = new OrderService();
        $this->apiClient = new LpostClient();
        $this->mailSender = new AbstractMailSender();
    }

    public function sendNewOrders(): void
    {
        $this->service->setParams([
            'needSend' => true,
        ]);
        $orderList = $this->service->getOrders();

        if (empty($orderList['orders'])) die();

        foreach ($orderList['orders'] as $order) {
            $this->service->setParams(['id' => $order['id']]);
            $fullOrder = $this->service->getOrderFull();
            $result = $this->apiClient->createOrder($fullOrder);

            Logger::getInstance()->writeLog('actionSendOrder.log', 'Send result: '  . json_encode($result));

            $newStatus = !empty($result) && $result[0]['Success'] ?
                OrderRepository::ORDER_STATUS_CREATED :
                OrderRepository::ORDER_STATUS_ERROR;

            if ($newStatus === OrderRepository::ORDER_STATUS_ERROR) {
                $this->mailSender->sendErrorsMail("Ошибка заказа при автоматической отправке", "Ошибка заказа ID: {$order['id']}");
            }

            $this->service->setParams([
                'id' => $order['id'],
                'status' => $newStatus
            ]);
            $isUpdate = $this->service->updateOrderStatus() ? 'true' : 'false';

            Logger::getInstance()->writeLog(
                'actionSendOrder.log',
                "Update order ". $order['id']." status result: " . $isUpdate
            );
        }
    }

    public function sendOrder(int $orderId): bool
    {
        $this->service->setParams([
            'id' => $orderId,
        ]);

        $result = $this->apiClient->createOrder($this->service->getOrderFull());
        $newStatus = OrderRepository::ORDER_STATUS_ERROR;
        if (!empty($result) && $result[0]['Success']) {
            $newStatus = OrderRepository::ORDER_STATUS_CREATED;
        }

        if ($newStatus === OrderRepository::ORDER_STATUS_ERROR) {
            $this->mailSender->sendErrorsMail("Ошибка заказа при ручной отправке", "Ошибка заказа ID: {$orderId}");
        }

        $this->service->setParams([
            'id' => $orderId,
            'status' => $newStatus
        ]);
        $this->service->updateOrderStatus();

        return $newStatus === OrderRepository::ORDER_STATUS_CREATED;
    }

    public function updateOrder(): void
    {
        $ordersIds = [];
        $ordersStatusForCheck = [];
        $ordersData = [];
        $api = new LpostClient();

        $this->service->setParams([
            'statuses' => [
                OrderRepository::ORDER_STATUS_CREATED,
                OrderRepository::ORDER_STATUS_ASSEMBLY,
                OrderRepository::ORDER_STATUS_SHIPPED,
                OrderRepository::ORDER_STATUS_ACCEPTED_IN_THE_COURIER,
                OrderRepository::ORDER_STATUS_AVAILABLE_FOR_ISSUANCE,
            ],
            'withPagination' => false
        ]);

        $orders = $this->service->getOrders();
        Logger::getInstance()->writeLog('actionUpdateOrder.log', 'Order list: ' . json_encode($orders));

        if (!empty($orders['orders'])) {
            foreach ($orders['orders'] as $order) {
                $ordersIds[$order['order_number']] = $order['id'];
                $ordersData[] = ['ID_Order' => $order['order_number']];
                $ordersStatusForCheck[$order['order_number']] = $order['status'];
            }

            foreach (array_chunk($ordersData, 50) as $ordersDataChunk) {
                $ordersInfo = $api->getOrderStatus($ordersDataChunk) ?: [];
                Logger::getInstance()->writeLog('actionUpdateOrder.log', 'Order statuses: ' . json_encode($ordersInfo));

                foreach ($ordersInfo as $orderInfo) {
                    $newStatus = isset($orderInfo['ID_StatusDelivery']) && is_numeric($orderInfo['ID_StatusDelivery']) ?
                        $this->formatOrderStatus($orderInfo['ID_StatusDelivery']) :
                        OrderRepository::ORDER_STATUS_ERROR;

                    if ($ordersStatusForCheck[$orderInfo['ID_Order']] == $newStatus) {
                        continue;
                    }

                    if ($newStatus === OrderRepository::ORDER_STATUS_ERROR) {
                        $this->mailSender->sendErrorsMail("Ошибка при обновлении статуса заказа", "Ошибка заказа ID: {$ordersIds[$orderInfo['ID_Order']]}");
                    }

                    $this->service->setParams([
                        'id' => $ordersIds[$orderInfo['ID_Order']],
                        'status' => $newStatus,
                        'date_storage' => $orderInfo['StoragePeriod'] ?
                            date('Y-m-d', strtotime($orderInfo['StoragePeriod'])) :
                            null,
                        'delivery_date' => $orderInfo['DateDeliv'] ?
                            date('Y-m-d', strtotime($orderInfo['DateDeliv'])) :
                            null,
                    ]);

                    $isUpdated = $this->service->updateOrderStatus() ? 'true' : 'false';
                    Logger::getInstance()->writeLog('actionUpdateOrder.log', 'Order is updated: ' . $isUpdated);
                }
            }
        }
    }

    private function formatOrderStatus(int $orderStatusFromLogist): int
    {
        $arr = [
            1 => OrderRepository::ORDER_STATUS_CREATED,
            2 => OrderRepository::ORDER_STATUS_ASSEMBLY,
            3 => OrderRepository::ORDER_STATUS_SHIPPED,
            4 => OrderRepository::ORDER_STATUS_ACCEPTED_IN_THE_COURIER,
            6 => OrderRepository::ORDER_STATUS_AVAILABLE_FOR_ISSUANCE,
            7 => OrderRepository::ORDER_STATUS_COMPLETED,
            8 => OrderRepository::ORDER_STATUS_REJECT,
        ];

        return $arr[$orderStatusFromLogist] ?? 0;
    }
}