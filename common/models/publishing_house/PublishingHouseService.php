<?php

namespace common\models\publishing_house;

use common\models\api\AWSClient;
use common\models\series\SeriesRepository;

class PublishingHouseService
{
    protected AWSClient $awsClient;
    protected PublishingHouseRepository $publishingHouseRepository;
    protected SeriesRepository $seriesRepository;

    public function __construct()
    {
        $this->awsClient = new AWSClient();
        $this->publishingHouseRepository = new PublishingHouseRepository();
        $this->seriesRepository = new SeriesRepository();
    }

    public function setParams($params): void
    {
        $this->publishingHouseRepository->setParams($params);
    }

    public function setOrder($order): void
    {
        $this->publishingHouseRepository->setOrder($order);
    }

    public function getPublishingHouses(): array
    {
        $publishers = $this->publishingHouseRepository->getPublishingHouses();

        foreach ($publishers['publishers'] as &$publisher) {
            $this->seriesRepository->setParams(['publishingHouseId' => $publisher['id']]);
            $publisher['series'] = $this->seriesRepository->getSeries();
        }

        return $publishers;
    }

    public function getPublishingHousesByCatalog(): array
    {
        $list = $this->publishingHouseRepository->getPublishingHouses()['publishers'];
        foreach ($list as &$item) {
            $item = PublishingHouseDTO::make($item);
        }
        return $list;
    }

    /**
     * @return array|null
     */
    public function getBigCard(): array|null
    {
        if (!$publisher = $this->publishingHouseRepository->getPublishingHouses()['publishers'][0]) {
            return null;
        }

        return $publisher;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $this->publishingHouseRepository->setParams($data);

        if (isset($this->publishingHouseRepository->create()['result'])) {

            $data['id'] += $this->publishingHouseRepository->getLastId();

            $this->saveImage($data);

            $this->publishingHouseRepository->setParams($data);
            return $this->publishingHouseRepository->update($data['id'])['result'] ?: false;
        }

        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        $this->saveImage($data);
        $this->publishingHouseRepository->setParams($data);

        return $this->publishingHouseRepository->update($data['id'])['result'] ?: false;
    }

    /**
     * @param $data
     * @return void
     */
    private function saveImage(&$data): void
    {
        if (isset($data['file'])) {

            if (isset($data['file']['name']) && isset($data['file']['tmp_name'])) {

                $name = 'publishers/' . $data['id'] . '/' . 'cover.jpg';

                $imgExist = $this->awsClient->savePhoto([
                    'Bucket' => \Yii::$app->params['aws_bucket'],
                    'SourceFile' => $data['file']['tmp_name'],
                    'Key' => $name
                ]);

                if ($imgExist) {
                    $data['cover'] = 'https://' . \Yii::$app->params['aws_bucket'] . '/' . $name;
                }
            }
        }
    }
}