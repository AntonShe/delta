<?php

namespace common\models\delivery_profile;

use common\models\AbstractRepository;
use common\models\api\lpostApi\LpostClient;
use common\models\points\PointService;
use common\models\user\UserRepository;
use common\models\user\UserService;
use yii\db\ActiveRecord;

class DeliveryProfileService
{
    protected DeliveryProfileRepository $repository;
    protected UserService $userService;
    protected PointService $pointService;
    protected LpostClient $lpostApi;

    public function __construct()
    {
        $this->repository = new DeliveryProfileRepository();
        $this->userService = new UserService();
        $this->pointService = new PointService();
        $this->lpostApi = new LpostClient();
    }

    public function setParams(array $params): void
    {
        $this->repository->setParams($params);
    }

    public function getCityList(): array
    {
        return $this->pointService->getAllCities();
    }

    protected function getUserSearchParams(): array
    {
        $params = [];
        $userId = \Yii::$app->user->getId() ?? 0;

        if ($userId === 0) {
            $identifier = \Yii::$app->session->get(\Yii::$app->user::TOKEN_KEY);

            if (is_null($identifier)) {
                $identifier = \Yii::$app->request->cookies->get(\Yii::$app->user::TOKEN_KEY);
            }

            if (!is_null($identifier)) {
                $params['userToken'] = is_string($identifier) ? $identifier : $identifier->value;;
            }
        } else {
            $params['userId'] = $userId;
        }

        return $params;
    }

    public function create(): bool
    {
        $this->repository->mergeParams($this->getUserSearchParams());
        $newProfile = $this->repository->createProfile();

        return !empty($newProfile);
    }

    public function getLastProfile(): array
    {
        $this->repository->mergeParams($this->getUserSearchParams());

        $deliveryProfiles = [
            'courier' => $this->repository->getProfile($this->repository::DELIVERY_TYPES['courier'])[0],
            'point' => $this->repository->getProfile($this->repository::DELIVERY_TYPES['point'])[0],
        ];

        if (empty($deliveryProfiles['courier']) && empty($deliveryProfiles['point'])) return [];

        if (!empty($deliveryProfiles['courier'])) {
            $rawCoordinates = json_decode($deliveryProfiles['courier']['coordinates'], true);
            $deliveryProfiles['courier']['latitude'] = $rawCoordinates[0];
            $deliveryProfiles['courier']['longitude'] = $rawCoordinates[1];

            $deliveryCourierData = $this->lpostApi->calculate([
                'latitude' => $deliveryProfiles['courier']['latitude'],
                'longitude' => $deliveryProfiles['courier']['longitude'],
            ]);
            $deliveryProfiles['courier']['price'] = $deliveryCourierData['JSON_TXT'][0]['SumCost'];
            $deliveryProfiles['courier']['date'] = $deliveryCourierData['JSON_TXT'][0]['DateClose'];
            $deliveryProfiles['courier']['comment'] = '';
        }

        if (!empty($deliveryProfiles['point'])) {
            $rawCoordinates = json_decode($deliveryProfiles['point']['coordinates'], true);
            $deliveryProfiles['point']['latitude'] = $rawCoordinates[0];
            $deliveryProfiles['point']['longitude'] = $rawCoordinates[1];

            $deliveryPointData = $this->lpostApi->calculate(['idPoint' => $deliveryProfiles['point']['pointId']]);
            $deliveryProfiles['point']['price'] = $deliveryPointData['JSON_TXT'][0]['SumCost'];
            $deliveryProfiles['point']['date'] = $deliveryPointData['JSON_TXT'][0]['DateClose'];
        }

        return $deliveryProfiles;
    }
}