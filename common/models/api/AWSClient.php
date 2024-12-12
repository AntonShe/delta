<?php

namespace common\models\api;

use Aws\S3\S3Client;

class AWSClient
{
    protected S3Client $client;

    public function __construct()
    {
        $this->client = new S3Client([
            'use_path_style_endpoint' => true,
            'version' => 'latest',
            'region' => \Yii::$app->params['aws_region'],
            'endpoint' => \Yii::$app->params['aws_endpoint_url'],
            'credentials' => [
                'key' => \Yii::$app->params['aws_access_key'],
                'secret' => \Yii::$app->params['aws_secret_access_key'],
            ]
        ]);
    }

    /**
     * @return S3Client
     */
    public function getClient(): S3Client
    {
        return $this->client;
    }

    /**
     * @param array $params
     * @return bool|null
     */
    public function savePhoto(array $params): ?bool
    {
        try {
            if (empty($params['SourceFile']) || empty($params['Bucket']) || empty($params['Key'])) {
                return false;
            }
            $this->client->putObject($params)->toArray();
            return $this->checkExistPhoto([
                'Bucket' => $params['Bucket'],
                'Key' => $params['Key']
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array $params
     * @return bool|null
     */
    public function checkExistPhoto(array $params): ?bool
    {
        try {
            if (empty($params['Bucket']) || empty($params['Key'])) {
                return false;
            }

            $result = $this->client->headObject($params)->toArray();

            if (isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == '200') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}