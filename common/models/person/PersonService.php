<?php

namespace common\models\person;

use common\models\api\AWSClient;

class PersonService
{

    protected PersonRepository $personRepository;
    protected AWSClient $awsClient;

    public function __construct()
    {
        $this->awsClient = new AWSClient();
        $this->personRepository = new PersonRepository();
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->personRepository->setParams($params);
    }

    /**
     * @return array|null
     */
    public function getBigCard(): array|null
    {
        if (!$person = $this->personRepository->getPersons()['persons'][0]) {
            return null;
        }

        return $person;
    }

    /**
     * @return array
     */
    public function getProductIdsByPerson(): array
    {
        if (!$productIds = $this->personRepository->getProductIds()) {
            return [];
        }
        $ids = [];
        foreach ($productIds as $productId) {
            $ids[] = $productId['product_id'];
        }

        return $ids;
    }

    /**
     * @return array
     */
    public function getPersons(): array
    {
        return $this->personRepository->getPersons();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function updatePerson(array $data): bool
    {
        $this->saveImage($data);
        $this->personRepository->setParams($data);

        return $this->personRepository->update();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function createPerson(array $data): bool
    {
        $this->personRepository->setParams($data);

        if ($this->personRepository->create()) {

            $data['id'] += $this->personRepository->getLastId();

            $this->saveImage($data);

            $this->personRepository->setParams($data);
            return $this->personRepository->update();
        }

        return false;
    }

    /**
     * @param $data
     * @return void
     */
    private function saveImage(&$data): void
    {
        if (isset($data['file'])) {

            if (isset($data['file']['name']) && isset($data['file']['tmp_name'])) {

                $name = 'persons/' . $data['id'] . '/' . 'cover.jpg';

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