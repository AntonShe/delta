<?php

namespace common\models\api;
use common\models\api\tokens\TokenService;
use CurlHandle;
use yii\base\Exception;

abstract class AbstractClient
{
    const REQUEST_TYPES = ['GET', 'POST', 'PUT'];

    protected CurlHandle $httpClient;

    protected string $apiUrl;

    protected string $apiToken;

    protected string $apiKey;

    protected string $apiTokenConfKey;

    protected string $apiOptionsConfKey;

    protected int $timeout;

    protected array $headers;

    protected bool $authorized;

    protected array $options;
    protected array $settings;

    protected int $tokenType;

    protected TokenService $tokenService;

    public function __construct()
    {
        if (!empty($this->apiTokenConfKey)) {
            $this->apiKey = \Yii::$app->params[$this->apiTokenConfKey];
        }

        if (!empty($this->apiOptionsConfKey)) {
            $this->options = \Yii::$app->params[$this->apiOptionsConfKey];
        }

        $this->httpClient = curl_init();
        $this->tokenService = new TokenService();
        $this->setTokenType();
        $this->authorized = $this->setToken();
    }

    public function __destruct()
    {
        curl_close($this->httpClient);
    }

    abstract protected function setTokenType(): void;

    abstract protected function authorization(): bool;

    abstract protected function prepareApiUrl(string $method): string;

    abstract protected function prepareApiParams(array $params): array;

    abstract protected function prepareResponse(string $response): array;

    private function setToken(): bool
    {
        $this->tokenService->setParams([
            'type' => $this->tokenType
        ]);
        $token = $this->tokenService->getToken();

        if (!empty($token)) {
            $this->apiToken = $token['token'];

            return true;
        }

        return false;
    }

    protected function checkAuthorization(): bool
    {
        if(!$this->authorized) {
            $this->authorized = $this->authorization();
        }
        return $this->authorized;
    }

    protected function callApi(string $type, string $method, array $params = []): array
    {
        if (!$this->checkAuthorization()) {
            throw  new \Exception('Not authorized');
        }

        $method = $this->prepareApiUrl($method);
        $params = $this->prepareApiParams($params);

        $response = $this->request($type, $method, $params);

        if (empty($response)) return [];

        return $this->prepareResponse($response);
    }

    final protected function request(string $type, string $method, array $params): string
    {
        if (!in_array($type, self::REQUEST_TYPES)) {
            throw new Exception("Не верный тип запроса {$type}");
        }

        $query = '';

        switch ($type) {
            case 'GET':
                $query .= '?' . http_build_query($params);
                break;

            case 'POST':
                $settings[CURLOPT_POST] = 1;
                $settings[CURLOPT_HTTPHEADER] = empty($this->headers) ? false : $this->headers;
                $settings[CURLOPT_POSTFIELDS] = http_build_query($params);
                break;
            case 'PUT':
                $settings[CURLOPT_CUSTOMREQUEST] = "PUT";
                $settings[CURLOPT_POSTFIELDS] = http_build_query($params);
                break;
        }

        $settings[CURLOPT_URL] = $method . $query;
        $settings[CURLOPT_RETURNTRANSFER] = 1;
        $settings[CURLOPT_SSL_VERIFYPEER] = 1;

        $settings = empty($this->settings) ? $settings : $settings + $this->settings;

        curl_setopt_array($this->httpClient, $settings);

        return curl_exec($this->httpClient);
    }
}