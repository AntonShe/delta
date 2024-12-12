<?php

namespace common\models\points;

use common\models\api\lpostApi\LpostClient;

class PointService
{
    const POINT_PHONE = '8-800-700-10-06';

    protected PointRepository $pointRepository;

    public function __construct()
    {
        $this->pointRepository = new PointRepository();
    }

    public function updatePointsFromApi(): bool
    {
        $apiClient = new LpostClient();
        $points = $apiClient->getPointsData()['PickupPoint'];

        if (empty($points)) return false;

        $this->pointRepository->deactivateAllPoints();

        foreach ($points as  $rawPoint) {
            $point = $this->prepareDataFromApi($rawPoint);
            $this->pointRepository->setParams($point);
            if (!$this->pointRepository->createItem()) return false;
        }

        return true;
    }

    protected function prepareDataFromApi($data): array
    {
        $preparedPoint = [
            'idPoint' => $data['ID_PickupPoint'],
            'latitude' => $data['Latitude'],
            'longitude' => $data['Longitude'],
            'cityName' => $data['CityName'],
            'idRegion' => $data['ID_Region'],
            'isCourier' => $data['IsCourier'],
            'isCash' => $data['IsCash'],
            'isCard' => $data['IsCard'],
            'address' => $data['Address'],
            'locationDescription' => $data['PickupDop'],
            'metro' => $data['Metro'],
            'fittingRoomsCount' => $data['NumberOfFittingRooms'] ?? 0,
            'isContentRequires' => $data['IsContentRequired'],
            'phone' => self::POINT_PHONE,
            'isDel' => 0,
        ];

        if (!empty($data['Photo'])) {
            $photos = [];

            foreach ($data['Photo'] as $photo) {
                $photos[] = $photo['Photo'] ?? [];
            }

            $preparedPoint['photos'] = json_encode($photos);
        } else {
            $preparedPoint['photos'] = null;
        }

        $days = [];

        foreach ($data['PickupPointWorkHours'] as $day) {
            $days[] = [
                'day' => $day['DayNumber'] - 1,
                'from' => $day['From'],
                'to' => $day['To']
            ];
        }

        $preparedPoint['workHours'] = json_encode($days);

        if ($preparedPoint['isCourier'] && !empty($data['Zone'][0]['WKT'])) {
            $rawPolygons = json_decode('{' . $data['Zone'][0]['WKT'] . '}', true);

            if ($rawPolygons['GeometryType'] == 'MultiPolygon') {
                $preparedPoint['courierPolygons'] = json_encode($rawPolygons['Coordinates']);
            } else {
                $polygons = [];
                $firstPoint = [];
                $rawPolygon = [];

                foreach ($rawPolygons['Coordinates'][0] as $polygonPoint) {
                    if (empty($firstPoint)) {
                        $firstPoint = $rawPolygon[] = $polygonPoint;
                        continue;
                    }

                    $rawPolygon[] = $polygonPoint;

                    if ($firstPoint[0] == $polygonPoint[0] && $firstPoint[1] == $polygonPoint[1]) {
                        $polygons[] = $rawPolygon;
                        $rawPolygon = $firstPoint = [];
                    }
                }

                $preparedPoint['courierPolygons'] = json_encode($polygons);
            }
        } else {
            $preparedPoint['courierPolygons'] = null;
        }

        return $preparedPoint;
    }

    public function getAllCities(): array
    {
        return $this->pointRepository->getAllCities();
    }

    public function getAllPoints(): array
    {
        return $this->pointRepository->getAllPoints();
    }

    public function getCourierDeliveryKindByCity(string $cityName): int
    {
        $courierPoint =  $this->pointRepository->getCourierByCity($cityName);

        return empty($courierPoint) ? -1 : $courierPoint['id_point'];
    }
}