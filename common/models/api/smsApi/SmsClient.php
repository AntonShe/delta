<?php

namespace common\models\api\smsApi;

use common\models\api\AbstractClient;

class SmsClient extends AbstractClient
{
    const MAX_MESSAGE_LENGTH = 70;

    protected string $apiUrl = 'https://smsc.ru/sys/send.php';

    protected string $apiOptionsConfKey = 'sms_api_key';

    public function __construct()
    {
        parent::__construct();

        $this->authorized = true;
    }

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
        return $this->apiUrl .= empty($method) ? '' : "/{$method}";
    }

    protected function prepareApiParams(array $params): array
    {
        return array_merge(
            $this->options,
            $params
        );
    }

    protected function prepareResponse(string $response): array
    {
        $responseData = explode(' SMS, ', $response);

        $status = explode(' - ', $responseData[0]);
        $id = explode(' - ', $responseData[1]);
        return [
            'status' => $status[0] == 'OK' && (int)$status[1] == 1,
            'id' => (int)$id[1]
        ];
    }

    public function sendMessage(string $phone, string $mess): array
    {
        $mess = trim($mess);

        if ($this->validatePhone($phone) && $this->validateMessage($mess)) {
            $response = $this->callApi('GET', '', [
                'phones' => $phone,
                'mes' => $mess
            ]);

            return $response;
        } else {
            return [];
        }
    }

    protected function validatePhone(string $phone): bool
    {
        $pattern = '/\+7\d{10}/';
        $matches =  [];

        preg_match($pattern, $phone, $matches);

        return (isset($matches[0]) && $matches[0] === $phone);
    }

    protected function validateMessage(string $text): bool
    {
        return (!empty($text) && strlen($text) <= self::MAX_MESSAGE_LENGTH);
    }
}