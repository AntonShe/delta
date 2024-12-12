<?php

namespace common\models\bitrix;

use common\models\AbstractRepository;
use common\models\genre\GenreService;
use common\models\language\LanguageService;
use common\models\obmb\OBMBRepository;
use Yii;

class BitrixRepository extends AbstractRepository
{
    const OLD_DELTABOOK_URL = 'https://deltabook.ru/';

    const EXCLUDE_GENRES = [
        898,
        960,
    ];
//    const BITRIX_PROPERTY_TYPE_COVER = 1;
    const BITRIX_BOOKS_TYPE = 2;
    const BITRIX_PROPERTY_TYPE_AUTHORS = 3;
    const BITRIX_PROPERTY_TYPE_YEAR = 5;
    const BITRIX_PROPERTY_TYPE_PAGES = 7;
    const BITRIX_PROPERTY_TYPE_ISBN = 8;
    const BITRIX_PROPERTY_TYPE_PUBLISHING_HOUSE = 9;
    const BITRIX_PROPERTY_TYPE_LEVEL = 18;
    const BITRIX_PROPERTY_TYPE_AGE = 19;
    const BITRIX_PROPERTY_TYPE_LAB_ID = 72;

    const BITRIX_PROPERTY_VALUE_TRUE = 'Y';

    protected yii\db\Connection $db;
    private array $genres = [];
    private array $languages = [];
    private array $obmbData = [];

    public function __construct()
    {
        $this->db = Yii::$app->get('db3');

        parent::__construct();
    }

    public function getGenres(): array
    {
        $output = [];
        try {
            $rows = $this->db->createCommand("
                    SELECT
                        id,
                        name,
                        depth_level as level,
                        sort,
                        iblock_section_id as parent_id,
                        description,
                        picture as cover
                    FROM bitrix.b_iblock_section
                        WHERE iblock_id = {$this->getBooksType()}
                            AND id not in ({$this->getExcludeGenresWhere()})
                    ORDER BY level;
                ")
                ->queryAll();

            foreach ($rows as &$row) {
                $row['isCourse'] = $row['description'] && $row['level'] > 2;
                $output[] = $row;
            }
        } catch (\Exception) {}

        return $output;
    }

    public function getPublishingHouses(): array
    {
        $output = [];
        try {
            $rows = $this->db->createCommand("
                    SELECT
                        id,
                        uf_name as name
                    FROM bitrix.b_publish
                    WHERE uf_name IS NOT NULL
                ")
                ->queryAll();

            foreach ($rows as $row) {
                $output[] = $row;
            }
        } catch(\Exception) {}

        return $output;
    }

    public function getLevels(): array
    {
        $output = [];
        try {
            $rows = $this->db->createCommand("
                    SELECT
                        id,
                        value AS name
                    FROM bitrix.b_iblock_property_enum
                    WHERE PROPERTY_ID = {$this->getBitrixPropertyTypeLevel()};
                ")
                ->queryAll();

            foreach ($rows as $row) {
                $output[] = $row;
            }
        } catch(\Exception) {}

        return $output;
    }

    public function getAges(): array
    {
        $output = [];
        try {
            $rows = $this->db->createCommand("
                    SELECT
                        id,
                        value AS name
                    FROM bitrix.b_iblock_property_enum
                    WHERE PROPERTY_ID = {$this->getBitrixPropertyTypeAge()};
                ")
                ->queryAll();

            foreach ($rows as $row) {
                $output[] = $row;
            }
        } catch(\Exception) {}

        return $output;
    }

    public function getProducts(): array
    {
        $output = [];
        try {
            $rows = $this->db->createCommand("
                    SELECT
                        product.id,
                        product.name,
                        catalog.quantity,
                        properties.iblock_property_id as propertyId,
                        GROUP_CONCAT(properties.value) as propertyValue,
                        product.detail_text,
                        product.active,
                        catalog.weight,
                        CONCAT(catalog.width, 'x', catalog.length, 'x', catalog.height) as size,
                        product.iblock_section_id as genre,
                        CONCAT(
                            IF(
                                ISNULL(files.external_id), 
                                'https://deltabook.ru/upload/', 
                                'https://labirint-deltabook.storage.yandexcloud.net/'
                            ),
                            files.subdir, '/', files.file_name
                        ) as img
                    FROM bitrix.b_iblock_element as product
                             JOIN bitrix.b_catalog_product AS catalog
                                  ON product.id = catalog.id
                             LEFT JOIN bitrix.b_iblock_element_property AS properties
                                       ON product.id = properties.iblock_element_id
                             LEFT JOIN bitrix.b_file AS files
                                       ON product.detail_picture = files.id
                    WHERE product.iblock_id = 2
                    GROUP BY product.id, properties.iblock_property_id;
                ")
                ->queryAll();

            // format properties
            $products = [];
            foreach ($rows as $row) {
                if (!isset($products[$row['id']])) {
                    $products[$row['id']] = $row;
                }

                $products[$row['id']]['properties'][$row['propertyId']] = $row['propertyValue'];
            }
            unset($rows);

            if (!empty($products)) {
                $this->genres = (new GenreService())->getGenres();
                $this->languages = (new LanguageService())->getLanguages();
                $this->obmbData = (new OBMBRepository())->getPricesAndQuantity();
            }

            foreach ($products as $product) {
                $genres = [];
                $labId = $product['properties'][self::BITRIX_PROPERTY_TYPE_LAB_ID] ?? '';
                $output[] = [
                    'id' => $product['id'],
                    'price' => $labId ? ($this->obmbData[$labId]['price'] ?? 0) : 0,
                    'labirintId' => $labId,
                    'title' => $product['name'],
                    'quantity' => $labId ? ($this->obmbData[$labId]['quantity'] ?? 0) : 0,
                    'isbn' => $product['properties'][self::BITRIX_PROPERTY_TYPE_ISBN] ?? '',
                    'publishingHouseId' => $product['properties'][self::BITRIX_PROPERTY_TYPE_PUBLISHING_HOUSE] ?? '',
                    'publishingYear' => $product['properties'][self::BITRIX_PROPERTY_TYPE_YEAR] ?? '',
                    'pagesNumber' => $product['properties'][self::BITRIX_PROPERTY_TYPE_PAGES] ?? '',
                    'annotation' => $product['detail_text'] ?: '',
                    'cover' => $product['img'] ?: '',
                    'active' => $product['active'] === self::BITRIX_PROPERTY_VALUE_TRUE,
                    'weight' => $product['weight'] ?: '',
                    'size' => $product['size'] ?: '',
                    'authors' => $product['properties'][self::BITRIX_PROPERTY_TYPE_AUTHORS] ?? '',
                    'genres' => $product['genre'] ? $genres = $this->getGenreParents($product['genre']) : [],
                    'levels' => $product['properties'][self::BITRIX_PROPERTY_TYPE_LEVEL] ?
                        \explode(',', $product['properties'][self::BITRIX_PROPERTY_TYPE_LEVEL]) : [],
                    'languages' => $this->getLanguage($genres),
                    'ages' => $product['properties'][self::BITRIX_PROPERTY_TYPE_AGE] ?
                        \explode(',', $product['properties'][self::BITRIX_PROPERTY_TYPE_AGE]) : [],
                    'isNew' => 0,
                ];
            }

        } catch (\Exception) {}

        return $output;
    }

    private function getLanguage(array $genres): array
    {
        $genreId = array_intersect($genres, array_column($this->genres, 'id'))[0];

        if (isset($this->genres[$genreId])) {
            foreach ($this->languages as $language) {
                if ($language['name'] == $this->genres[$genreId]['name']) {
                    return [$language['id']];
                }
            }
        }

        return [];
    }

    private function getGenreParents($idGenre): array
    {
        $output = [$idGenre];

        foreach ($this->genres as $genre) {
            if ($genre['id'] === $idGenre && $genre['parentId']) {
                $output = \array_merge($output, $this->getGenreParents($genre['parentId']));
            }
        }

        return $output;
    }

    private function getExcludeGenresWhere(): string
    {
        return implode(',', self::EXCLUDE_GENRES);
    }

    private function getBooksType(): string
    {
        return self::BITRIX_BOOKS_TYPE;
    }

    private function getBitrixPropertyTypeLevel(): string
    {
        return self::BITRIX_PROPERTY_TYPE_LEVEL;
    }

    private function getBitrixPropertyTypeAge(): string
    {
        return self::BITRIX_PROPERTY_TYPE_AGE;
    }
}