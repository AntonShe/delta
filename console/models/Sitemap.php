<?php

namespace console\models;

use common\models\genre\GenreEntity;
use common\models\product\ProductEntity;
use common\models\xml\FailedSavingXML;
use common\models\xml\XmlCreator;

class Sitemap
{
    const DEFAULT_PAGES = [
        '',
        'promotion',
        'delivery',
        'contacts',
        'paymentRefund',
        'userAgreement',
        'cart',
    ];

    const ENTITIES = [
        'product',
        'catalog',
        'others'
    ];

    const LIMIT = 50000;

    private string $path = '';

    private array $files = [];

    private XmlCreator $xml;
    private string $host = 'https://deltabook.ru/';
    private ProductEntity $productEntity;
    private GenreEntity $genreEntity;
    private array $data = [
        [
            'title' => 'urlset',
            'attributes' => [
                'xmlns:xsi' =>'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            ]
        ],
    ];

    private array $generalData = [
        [
            'title' => 'sitemapindex',
            'attributes' => [
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            ]
        ],
    ];

    public function __construct()
    {
        $this->productEntity = new ProductEntity();
        $this->genreEntity = new GenreEntity();
    }


    /**
     * @return void
     * @throws FailedSavingXML
     */
    public function create(): void
    {
        foreach (self::ENTITIES as $entity) {
            $countEntity = $this->getCountEntity($entity);
            $countCiel = (int)ceil($countEntity/self::LIMIT)*self::LIMIT;

            $j = 1;
            for ($i = 0; $i < $countCiel; $i += self::LIMIT) {
                $data = $this->getData($entity, limit: self::LIMIT, offset: $i, numberDoc: $j);

                $this->files[] = $this->path;

                $this->xml = new XmlCreator($this->path);
                $this->xml->setData($data);

                if (!$this->xml->save()) {
                    throw new FailedSavingXML("Возникла ошибка при сохранении");
                }
                $j++;
            }
        }

        $data = $this->getData('general');
        $this->xml = new XmlCreator($this->path);
        $this->xml->setData($data);

        if (!$this->xml->save()) {
            throw new FailedSavingXML("Возникла ошибка при сохранении");
        }
    }

    /**
     * @param string $entity
     * @param int $limit
     * @param int $offset
     * @param int $numberDoc
     * @return array|array[]
     */
    private function getData(string $entity, int $limit = 0, int $offset = 0, int $numberDoc = 1): array
    {
        $data = [];
        switch ($entity) {
            case 'product':
                $productsIds = $this->productEntity::find()
                    ->select('id')
                    ->where(['active' => 1 , 'is_new' => 0])
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy(['id' => 'ASC'])
                    ->column();

                foreach ($productsIds as $productsId) {
                    $data['elements'][] = $this->getElement("{$this->host}product/$productsId");
                }

                $this->path = "frontend/web/sitemap-$entity-$numberDoc.xml";
                break;

            case 'catalog':
                $genresIds = $this->genreEntity::find()
                    ->select('id')
                    ->orderBy(['id' => 'ASC'])
                    ->column();

                foreach ($genresIds as $genresId) {
                    $data['elements'][] = $this->getElement("{$this->host}catalog/$genresId");
                }

                $this->path = "frontend/web/sitemap-$entity.xml";
                break;

            case 'others':
                foreach (self::DEFAULT_PAGES as $page) {
                    $data['elements'][] = $this->getElement("{$this->host}{$page}");
                }

                $this->path = "frontend/web/sitemap.xml";
                break;

            case 'general':
                foreach ($this->files as $file) {
                    $file = stristr($file, 'sitemap');
                    $data['elements'][] = $this->getGeneralElement("{$this->host}{$file}");
                }

                $this->path = "frontend/web/sitemap-index.xml";

                return [$this->generalData[0] + $data];
        }

        return [$this->data[0] + $data];
    }


    /**
     * @param string $value
     * @return array
     */
    private function getElement(string $value): array
    {
        return [
            'title' => 'url',
            'elements' => [
                [
                    'title' => 'loc',
                    'value' => $value
                ],
                [
                    'title' => 'lastmod',
                    'value' => $this->getDate()
                ],
            ]
        ];
    }

    /**
     * @param string $value
     * @return array
     */
    private function getGeneralElement(string $value): array
    {
        return [
            'title' => 'sitemap',
            'elements' => [
                [
                    'title' => 'loc',
                    'value' => $value
                ],
            ]
        ];
    }

    /**
     * @return string
     */
    private function getDate(): string
    {
        return (new \DateTime())->format(DATE_W3C);
    }

    /**
     * @param string $entity
     * @return int
     */
    private function getCountEntity(string $entity): int
    {
        $count = 0;
        switch ($entity) {
            case 'product':
                $count = $this->productEntity::find()
                    ->select('id')
                    ->where(['active' => 1 , 'is_new' => 0])
                    ->count();

                break;
            case 'catalog':
                $count = $this->genreEntity::find()
                    ->select('id')
                    ->where('level > 1')
                    ->count();

                break;
            case 'others':
                $count = 1;

                break;
        }

        return $count;
    }
}