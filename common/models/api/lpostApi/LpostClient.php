<?php

namespace common\models\api\lpostApi;

use common\models\api\AbstractClient;
use common\models\logger\Logger;
use common\models\points\PointService;
use DateTime;

class LpostClient extends AbstractClient
{
    const TOKEN_TYPE = 1;
    const ID_WHENCE = 22;
    const ALIQUOT = 5;

    private PointService $pointService;

    protected string $apiUrl = 'https://api.l-post.ru/';
    protected string $apiTokenConfKey = 'lpost_api_key';
    protected array $headers = [
        'Accept */*',
        'Cache-Control: no-cache',
        'Content-Type: application/x-www-form-urlencoded'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->pointService = new PointService();
    }

    protected function setTokenType(): void
    {
        $this->tokenType = self::TOKEN_TYPE;
    }

    protected function authorization(): bool
    {
        $response = $this->request(
            'POST',
            $this->apiUrl,
            [
                'method' => 'Auth',
                'secret' => $this->apiKey
            ]
        );

        $data = json_decode($response, true);

        if (!empty($data['errorMessage'])) return false;

        $this->apiToken = $data['token'];

        $this->tokenService->setParams([
            'token' => $this->apiToken,
            'valid_till' => date('Y-m-d H:i:s', strtotime($data['valid_till'])),
            'type' => $this->tokenType
        ]);

        return $this->tokenService->createToken();
    }

    protected function prepareApiUrl(string $method): string
    {
        return $this->apiUrl;
    }

    protected function prepareApiParams(array $params): array
    {
        $params['token'] = $this->apiToken;

        return $params;
    }

    protected function prepareResponse(string $response): array
    {
        $data = json_decode($response, true);
        Logger::getInstance()->writeLog('LpostApiResponse.log', 'API Response: ' .  $response);

        try {
            if (empty($data['Message'])) {
                $result = $data['JSON_TXT'] ? json_decode($data['JSON_TXT'], true) : [];

                $this->calculateDeliveryDate($result);
                $this->roundPrice($result);

                return $result;
            }
        } catch (\Throwable $e) {
            Logger::getInstance()->writeLog('LpostApiError.log', 'Error: ' .  $e->getMessage());
        }

        return [];
    }

    public function getPointsData(): array
    {
        return $this->callApi('GET', '', [
            'method' => 'GetPickupPoints',
            'ver' => 1,
            'json' => '{}'
        ]);
    }

    public function calculate(array $data): array
    {
        $json = [];

        if (!empty($data['idPoint']) || $data['idPoint'] === 0) {
            $json['ID_PickupPoint'] = $data['idPoint'];
        } else if (!empty($data['latitude']) && !empty($data['longitude'])) {
            $json['Latitude'] = $data['latitude'];
            $json['Longitude'] = $data['longitude'];
        } else {
            return [];
        }

        return $this->callApi('GET', '', [
            'method' => 'GetServicesCalc',
            'ver' => 1,
            'json' => json_encode($json)
        ]);
    }

    public function createOrder(array $data): array
    {
        $preparedData = $this->prepareBeforeCreateOrder($data);
        Logger::getInstance()->writeLog('actionSendOrder.log', 'Order json: ' .  json_encode($preparedData));

        return $this->callApi('POST', '', [
            'method' => 'ShopIMCreateOrders',
            'ver' => 1,
            'json' => json_encode($preparedData)
        ]);
    }

    public function rejectOrder(array $data): array
    {
        return $this->callApi('PUT', '', [
            'method' => 'ShopIMUpdateOrders',
            'ver' => 1,
            'json' => json_encode($data)
        ]);
    }

    public function getOrderStatus(array $data): array
    {
        return $this->callApi('GET', '', [
            'method' => 'ShopIMGetOrdersInfo',
            'ver' => 1,
            'json' => json_encode($data)
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFinancialInfo(): array
    {
        $result = $this->callApi('GET', '', [
            'method' => 'ShopIMGetFinancialInfo',
            'ver' => 1,
            'json' => '{}'
        ]);
        return $result['Balance'];
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateFinancialInfo(array $data): array
    {
        return $this->callApi('GET', '', [
            'method' => 'ShopIMReturnedBalanceToCard',
            'ver' => 1,
            'json' => json_encode($data)
        ]);
    }

    private function prepareBeforeCreateOrder(array $data): array
    {
        $result = [
            'BooksPrice' => 0,
            'DateCreate' => (new DateTime($data['status']['date_create']))->format('Y-m-d\TH:i:s'),
            'ID_Contact' => $data['user']['id'],
            'ID_Order' => $data['status']['orderNumber'],
            'ID_Whence' => self::ID_WHENCE,
            'Items' => [],
            'MovePrice' => $data['delivery']['price'],
            'FIO' => implode(' ', [
                $data['user']['lastName'],
                $data['user']['firstName'],
                $data['user']['secondName'],
            ])
        ];

        foreach ($data['products'] as $product) {
            $result['Items'][] = [
                'ID_Product' => $product['labirintId'],
                'Price' => $product['priceInOrder'],
                'Quantity' => $product['quantityCart'],

            ];
            $result['BooksPrice'] += $product['priceInOrder'];
        }

        if ($data['user']['profile'][0]['isLegal'] == 1) {
            $result['PaymentKind'] = 1;
            $result['PayFromCart'] = 0;
        } else {
            $result['PaymentKind'] = $data['paymentType'] == 1 ? 10 : 0;
            $result['PayFromCart'] = $data['paymentType'] == 1
                ? $result['BooksPrice'] + $data['delivery']['price'] : 0;
        }

        if ($data['delivery']['type'] == 2) {
            $result['DeliveryKind'] = $data['delivery']['pointId'];
        } else {
            $result['DeliveryKind'] = $this->getDeliveryKindForCourier($data['delivery']['city']);
            $result['Address'] = $data['delivery']['address'];
            $result['Latitude'] = $data['delivery']['latitude'];
            $result['Longitude'] = $data['delivery']['longitude'];

            if ($data['delivery']['flat']) $result['Flat'] = $data['delivery']['flat'];

            if ($data['delivery']['entryCode']) $result['Code'] = $data['delivery']['entryCode'];

            if ($data['delivery']['courierComment']) $result['Comment'] = $data['delivery']['courierComment'];
        }

        $result['RecipientFIO'] = $data['subUser']['subLastName'] ?: $data['user']['lastName'] . ' ';
        $result['RecipientFIO'] .= $data['subUser']['subFirstName'] ?: $data['user']['firstName'];

        $matches = [];
        $phone = $data['subUser']['subPhone']?:$data['user']['phone'];
        $phone = str_replace('+7', '', $phone);
        preg_match_all('/[0-9]/', $phone, $matches);
        $result['RecipientPhone'] = implode('', $matches[0]);

        if ($data['user']['phone']) {
            $matches = [];
            $data['user']['phone'] = str_replace('+7', '', $data['user']['phone']);
            preg_match_all('/[0-9]/', $data['user']['phone'], $matches);
            $result['Phone'] = implode('', $matches[0]);
        }

        if ($data['user']['email']) $result['Email'] = $data['user']['email'];

        if ($data['user']['profile'][0]['sex'] !== '') $result['Sex'] = (bool)$data['user']['profile'][0]['sex'];

        if ($data['user']['profile'][0]['isLegal'] == 1) {
            $result['LegalInfo']['PayerInfo'] = [
                'TypeOfLegal' => 0,
                'INN' => $data['user']['profile'][0]['legalInn'] ?? '',
                'KPP' => $data['user']['profile'][0]['legalKpp'] ?? '',
                'FullName' => $data['user']['profile'][0]['legalName'] ?? '',
                'BankName' => $data['user']['profile'][0]['legalBank'] ?? '',
                'NumberCorr' => $data['user']['profile'][0]['legalCorAcc'] ?? '',
                'BIK' => $data['user']['profile'][0]['legalBik'] ?? '',
                'Address' => $data['user']['profile'][0]['legalAddress'] ?? '',
                'NumberAccount' => $data['user']['profile'][0]['legalCheckingAcc'] ?? '',
                'PersonalAccount' => $data['user']['profile'][0]['legalBankBook'] ?? '',
                'PositionSign' => $data['user']['profile'][0]['legalSignatoryPosition'] ?? '',
                'FIOSign' => $data['user']['profile'][0]['legalSignatoryName'] ?? '',
                'ActsOnBasis' => $data['user']['profile'][0]['legalSignatoryBase'] ?? '',
                'LegalSpecification' => 1,
                'LegalRegistrationDoc' => 1,
                'LegalSpecNum' => 1,
                'LegalUstav' => 1,
            ];

            if (!empty($data['user']['profile'][1])) {
                $result['LegalInfo']['ConsigneeInfo'] = [
                    'TypeOfLegal' => 0,
                    'INN' => $data['user']['profile'][0]['legalInn'] ?? '',
                    'KPP' => $data['user']['profile'][0]['legalKpp'] ?? '',
                    'FullName' => $data['user']['profile'][0]['legalName'] ?? '',
                    'BankName' => $data['user']['profile'][0]['legalBank'] ?? '',
                    'NumberCorr' => $data['user']['profile'][0]['legalCorAcc'] ?? '',
                    'BIK' => $data['user']['profile'][0]['legalBik'] ?? '',
                    'Address' => $data['user']['profile'][0]['legalAddress'] ?? '',
                    'NumberAccount' => $data['user']['profile'][0]['legalCheckingAcc'] ?? '',
                    'PersonalAccount' => $data['user']['profile'][0]['legalBankBook'] ?? '',
                ];
            }
        }

        return [
            'Orders' => [$result]
        ];
    }

    private function getDeliveryKindForCourier(string $cityName): int
    {
        return $this->pointService->getCourierDeliveryKindByCity($cityName);
    }

    private function calculateDeliveryDate(array &$data): void
    {
        if (isset($data['JSON_TXT'][0]['PossibleDelivDates'])) {

            $loadDate = strtotime($data['JSON_TXT'][0]['PossibleDelivDates'][0]['DateDelive']);
            $data['JSON_TXT'][0]['DateClose'] = date('Y-m-d\TH:i:s', $loadDate);

        } elseif (isset($data['JSON_TXT'][0]['DateClose'])) {

            $loadDate = strtotime($data['JSON_TXT'][0]['DateClose']);
            $logisticDays = $data['JSON_TXT'][0]['DayLogistic'] * 86400;
            $data['JSON_TXT'][0]['DateClose'] = date('Y-m-d\TH:i:s', $loadDate + $logisticDays);
        }
    }

    private function roundPrice(array &$data): void
    {
        if (
            isset($data['JSON_TXT'][0]['SumCost'])
            && ($data['JSON_TXT'][0]['SumCost'] % self::ALIQUOT) > 0
        ) {
            $addCost = self::ALIQUOT - ($data['JSON_TXT'][0]['SumCost'] % self::ALIQUOT);
            $data['JSON_TXT'][0]['SumCost'] += $addCost;
        }
    }
}