<?php

namespace console\models;

use common\models\api\AWSClient;
use common\models\product\ProductRepository;
use common\models\product_image\ProductImageRepository;
use yii\db\Exception;

class ImageTransporter
{

    protected ProductRepository $productRepository;
    protected ProductImageRepository $productImageRepository;
    protected AWSClient $awsClient;

    public function __construct()
    {
        $this->awsClient = new AWSClient();
        $this->productRepository = new ProductRepository();
        $this->productImageRepository = new ProductImageRepository();
    }


    /**
     * @return void
     * @throws Exception
     */
    public function movePhotoToCloud(): void
    {
        $products = $this->productRepository->getCoverProducts();

        print_r('Всего найдено товаров: ' . count($products) . PHP_EOL);

        if (!file_exists('/var/www/images')) {
            mkdir('/var/www/images', 0777, true);
        }

        if (!file_exists('/var/www/images/products')) {
            mkdir('/var/www/images/products', 0777, true);
        }
        $c = 0;
        foreach ($products as $product) {
            print_r('Id товара: ' . $product['id'] . PHP_EOL);
            $name = 'products/' . $product['id'] . '/cover.jpg';
            if (!file_exists('/var/www/images/products/' . $product['id'])) {
                mkdir('/var/www/images/products/' . $product['id'], 0777, true);
            }

            $path = '/var/www/images/' . $name ;

            if (!$product['cover']) {
                $product['cover'] = 'https://img1.labirint.ru/books/' . $product['labirint_id'] .'/cover.jpg';
            }

            $response = get_headers($product['cover']);

            $imgExist = false;
            if ($response && strpos($response[0], '200')) {
                file_put_contents($path, file_get_contents($product['cover']));

                $imgExist = $this->awsClient->savePhoto([
                    'Bucket' => \Yii::$app->params['aws_bucket'],
                    'SourceFile' => $path,
                    'Key' => $name
                ]);
            }

            if ($imgExist) {
                $this->productRepository->setParams(['cover' => 'https://' . \Yii::$app->params['aws_bucket'] . '/' . $name]);
                $res = $this->productRepository->update($product['id']);

                if ($res['result']) {
                    $c++;
                }
            }
            print_r('Выполнено: ' . $c . ' из ' . count($products) . PHP_EOL);
        }
        print_r('Выполнено всего: ' . $c . ' из ' . count($products) . PHP_EOL);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function addAdditionalPhoto(): void
    {
        $urlLabirint = 'https://img1.labirint.ru/books/';
        $productImages = $this->productImageRepository->getDistinctProductsImages();

        $products = $this->productRepository->getProductsWithLabId(array_column($productImages, 'product_id'));

        print_r('Всего найдено товаров: ' . count($products) . PHP_EOL);

        if (!file_exists('/var/www/images')) {
            mkdir('/var/www/images', 0777, true);
        }

        if (!file_exists('/var/www/images/products')) {
            mkdir('/var/www/images/products', 0777, true);
        }

        $c = 0;
        foreach ($products as $product) {
            print_r('Id товара: ' . $product['id'] . PHP_EOL);

            foreach ($this->productImageRepository::LIST_IMAGES as $intType => $imageName) {
                $productImageRepository = new ProductImageRepository();

                $name = 'products/' . $product['id'] . '/' . $imageName;

                if (!file_exists('/var/www/images/products/' . $product['id'])) {
                    mkdir('/var/www/images/products/' . $product['id'], 0777, true);
                }

                $pathSave = '/var/www/images/' . $name ;

                $fileUrl = $urlLabirint . '/' . $product['labirint_id'] . '/' . $imageName;

                $response = get_headers($fileUrl);

                $imgExist = false;
                if ($response && strpos($response[0], '200')) {
                    file_put_contents($pathSave, file_get_contents($fileUrl));

                    $imgExist = $this->awsClient->savePhoto([
                        'Bucket' => \Yii::$app->params['aws_bucket'],
                        'SourceFile' => $pathSave,
                        'Key' => $name
                    ]);
                }

                if ($imgExist) {

                    $productImageRepository->setParams([
                        'url' => 'https://' . \Yii::$app->params['aws_bucket'] . '/' . $name,
                        'typeImg' => $intType,
                        'productId' => $product['id']
                    ]);

                    $productImageRepository->create();
                }
            }
            $c++;
            print_r('Выполнено: ' . $c . ' из ' . count($products) . PHP_EOL);
        }
        print_r('Выполнено всего: ' . $c . ' из ' . count($products) . PHP_EOL);
    }
}