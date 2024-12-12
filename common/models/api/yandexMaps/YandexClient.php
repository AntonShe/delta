<?php

namespace common\models\api\yandexMaps;

use common\models\api\AbstractClient;

class YandexClient extends AbstractClient
{
    protected string $apiUrl = 'https://geocode-maps.yandex.ru/1.x/';

    protected string $apiTokenConfKey = 'yandex_api_key';

    protected array $settings = [
        CURLOPT_REFERER => 'https://deltabook.ru/',
    ];

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
        $rawResult = json_decode($response, true);
        $found = $rawResult['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'];

        if ($found == 0) return [];

        $point = explode(' ', $rawResult['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);

        return [
            'perception' => $rawResult['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['precision'],
            'latitude' => $point[1],
            'longitude' => $point[0],
        ];
    }

    public function getSuggests(string $address)
    {
        return $this->callApi('GET', '', [
            'format' => 'json',
            'apikey' => $this->apiKey,
            'geocode' => $address
        ]);
    }
}