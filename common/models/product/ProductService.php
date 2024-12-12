<?php

namespace common\models\product;

use common\models\AbstractDecorator;
use common\models\age\AgeRepository;
use common\models\genre\GenreRepository;
use common\models\language\LanguageRepository;
use common\models\level\LevelRepository;
use common\models\product_image\ProductImageRepository;

class ProductService
{
    const GLOBAL_DISCOUNT = 0.2;//Временная скидка на весь ассортимент, позже появится система акций и скидок

    protected ProductRepository $productRepository;
    protected LevelRepository $levelRepository;
    protected AgeRepository $ageRepository;
    protected LanguageRepository $languageRepository;
    protected GenreRepository $genreRepository;
    protected ProductImageRepository $productImageRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->levelRepository = new LevelRepository();
        $this->ageRepository = new AgeRepository();
        $this->languageRepository = new LanguageRepository();
        $this->genreRepository = new GenreRepository();
        $this->productImageRepository= new ProductImageRepository();
    }

    public function setParams($params): void
    {
        $this->productRepository->setParams($params);
    }

    public function setOrder(array $orders): void
    {
        $this->productRepository->setOrder($orders);
    }

    public function getProducts(): array
    {
        $data = $this->productRepository->getProducts();

        foreach ($data['products'] as &$product) {
            $product = $this->setProductPrice($product);
        }

        return $data;
    }

    public function updateProduct(int $id): array
    {
        return $this->productRepository->update($id);
    }

    public function getProductsForCatalog(): array
    {
        $data = $this->productRepository->getProducts();

        foreach ($data['products'] as &$product) {
            $product = $this->setProductPrice($product);
            $product = ProductDTO::make($product);
            $product = ProductDecorator::decorate($product);
        }

        return $data;
    }

    public function getBigCard(): ?AbstractDecorator
    {
        if (!($product = $this->productRepository->getProducts()['products'][0] ?? [])) {
            return null;
        }
        $product = $this->setProductPrice($product);
        $this->setAdditionalImages($product);
        $product = ProductDTO::make($product);
        $product = ProductDecorator::decorate($product);

        return ProductBigCardDecorator::decorate($product);
    }

    private function setProductPrice($product): array
    {
        $product['oldPrice'] = $product['price'];
        $newPrice = (int)$product['price'] * (1 - self::GLOBAL_DISCOUNT);
        $product['price'] = intval(round($newPrice));

        return $product;
    }


    /**
     * @param int $page
     * @return array
     */
    public function getProductsForManticoreSearch(int $page): array
    {
        $products = $this->productRepository->getProductsForManticoreSearch($page);

        foreach ($products['products'] as &$product) {
            $product['languages'] = implode(',', array_column($this->productRepository->getLanguages($product['id']), 'id'));
            $product['levels'] = implode(',', array_column($this->productRepository->getLevels($product['id']), 'id'));
            $product['genres'] = $this->getStringGenres($product['id']);

            $product['publishingHouse'] = $product['publishingHouseId'] ?
                $this->productRepository->getPublishingHouseName($product['publishingHouseId']) : '';

            if ($product['isbn']) {

                $isbn = preg_replace('/[^0-9A-Za-z,]/', '', $product['isbn']) ;

                if ($product['isbn'] !== $isbn) {
                    $product['isbn'] .= "^" . $isbn;
                }
            }

            $product['ages'] = implode(',', array_column($this->productRepository->getAges($product['id']), 'id'));
            $product['authors'] = str_replace(', ', '^', $product['authors']);

            $product['title'] = $product['title'] ? $this->getPreparedData($product['title']) : '';
            $product['annotation'] = $product['annotation'] ? $this->getPreparedData(strip_tags($product['annotation'])) : '';
            $product['shortAnnotation'] = $product['shortAnnotation'] ? $this->getPreparedData(strip_tags($product['shortAnnotation'])) : '';
        }

        return $products;
    }

    /**
     * @param int $id
     * @return string
     */
    private function getStringGenres(int $id): string
    {
        $this->genreRepository->setParams(['productId' => $id]);

        return $this->genreRepository->getStringNameGenres();
    }


    /**
     * @param string $data
     * @return string
     */
    public function getPreparedData(string $data): string
    {
        $dataWithReplace = preg_replace("/['`]/", '', $data);
        $newData = preg_replace('/[^a-zа-яё0-9]/ui',' ', $dataWithReplace);

        return preg_replace("/\s+/", " ", $newData);

    }

    private function setAdditionalImages(&$product): void
    {
        $this->productImageRepository->setParams(['productId' => $product['id']]);
        $product['images'] = $this->productImageRepository->getUrlProductImages();
    }
}