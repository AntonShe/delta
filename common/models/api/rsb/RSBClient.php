<?php

namespace common\models\api\rsb;

use common\models\api\AbstractClient;
use common\models\logger\Logger;
use yii\base\Exception;

class RSBClient extends AbstractClient
{
    protected string $apiUrl = 'https://securepay.rsb.ru:9443/ecomm2/MerchantHandler';
    protected string $redirectUrl = 'https://securepay.rsb.ru/ecomm2/ClientHandler';

    protected array $settings = [
        CURLOPT_TIMEOUT_MS => 3000,
        CURLOPT_CONNECTTIMEOUT_MS => 3000,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSLCERT => self::PATH_FILE_PEM,
        CURLOPT_SSLKEY => self::PATH_FILE_KEY,
        CURLOPT_CAINFO => self::PATH_FILE_CHAIN
    ];

    protected array $headers = [
        'Accept */*',
        'Cache-Control: no-cache',
        'Content-Type: application/x-www-form-urlencoded'
    ];

    const PATH_FILE_PEM = __DIR__ . '/../../../ssl/rsb/9296401193.pem';
    const PATH_FILE_KEY = __DIR__ . '/../../../ssl/rsb/9296401193.key';
    const PATH_FILE_CHAIN = __DIR__ . '/../../../ssl/rsb/chain-ecomm-ca-root-ca.crt';

    protected function setTokenType(): void
    {
        $this->tokenType = 0;
    }

    protected function authorization(): bool
    {
        return true;
    }

    protected function prepareApiUrl(string $method): string
    {
        return $this->apiUrl;
    }

    protected function prepareApiParams(array $params): array
    {
        return $params;
    }

    protected function prepareResponse(string $response): array
    {
        Logger::getInstance()->writeLog('RSBApiResponse.log', 'API Response: ' .  $response, true);

        $result = [];
        $items = explode("\n", $response);

        foreach ($items as $item) {
            if (!empty($item)) {
                $data = explode(":", $item, 2);
                $result[trim($data[0])] = trim($data[1]);
            }

        }

        return $result;
    }


    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function getTransactionId(array $params): array
    {
        $response = $this->callApi('POST', '', $params);
        if ($response['TRANSACTION_ID']) {
            $payment['trans_id'] = $response['TRANSACTION_ID'];
            $payment['status'] = 'CREATED';
        } else {
            $message = "WARNING: Возникла ошибка при оплате Русский Стандарт. Заказ {$params['order_id']}, сумма {$params['amount']}";
            throw  new \Exception("Exception:" . $message);
        }
        return $payment;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function closeBusinessDay(): void
    {
        $response = $this->callApi('POST', '',['command' => 'b']);
        if ($response['RESULT'] !== 'OK') {
            $message = "WARNING: Закрытие бизнес-дня Русский Стандарт error " . $response['RESULT'];
            throw  new \Exception("Exception:" . $message);
        }
    }


    /**
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function getStatusTransaction(array $params): string
    {
        $response = $this->callApi('POST', '', $params);
        if (empty($response['error']) ) {
            return $response['RESULT'];
        } else {
            throw new \Exception("WARNING: Ошибка получения статуса Русский Стандарт: {$response['error']}.  
            Transaction_ID: {$params['trans_id']}");
        }
    }


    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function refundPayment(array $params): array
    {
        $response = $this->callApi('POST', '', $params);
        $result = [];
        if ($response['REFUND_TRANS_ID']) {
            $result['trans_id'] = $response['REFUND_TRANS_ID'];
            $result['status'] = $response['RESULT'];
            return $result;
        } else {
            $message = "WARNING: Возврат средств Русский Стандарт не был осуществлен. Transaction_ID: "
                . $params['trans_id'] .
                "RESULT: " . $response['error'];
            throw  new \Exception("Exception:" . $message);
        }

    }
}