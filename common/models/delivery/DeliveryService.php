<?php

namespace common\models\delivery;

use common\models\api\AbstractClient;
use common\models\api\lpostApi\LpostClient;
use common\models\api\yandexMaps\YandexClient;

class DeliveryService
{
    protected string $address;
    protected int $idPoint;
    protected float $latitude;
    protected float $longitude;
    protected AbstractClient $yandex;
    protected LpostClient $lpost;

    public function __construct()
    {
        $this->yandex = new YandexClient();
        $this->lpost = new LpostClient();
    }

    public function setParams(array $params): void
    {
        if (!empty($params['address'])) $this->address = 'Россия, ' . $params['address'];
        if (!is_null($params['idPoint'])) $this->idPoint = $params['idPoint'];
        if (!empty($params['latitude'])) $this->latitude = $params['latitude'];
        if (!empty($params['longitude'])) $this->longitude = $params['longitude'];
    }

    public function getSuggest(): array
    {
        return $this->yandex->getSuggests($this->address);
    }

    public function calculate(): array
    {

        $data = [
            'address' => $this->address ?? null,
            'idPoint' => $this->idPoint ?? null,
            'latitude' => $this->latitude ?? null,
            'longitude' => $this->longitude ?? null,
        ];

        return $this->lpost->calculate($data);
    }
}