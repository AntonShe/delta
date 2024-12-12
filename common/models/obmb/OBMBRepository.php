<?php

namespace common\models\obmb;

use common\models\AbstractRepository;
use common\models\age\AgeRepository;
use common\models\level\LevelRepository;
use common\models\person\PersonRepository;
use common\models\publishing_house\PublishingHouseRepository;
use common\models\series\SeriesRepository;
use Yii;
use yii\db\Exception;

class OBMBRepository extends AbstractRepository
{
    protected yii\db\Connection $db;
    protected yii\db\Connection $dbSite;
    protected array $allLevels = [];
    protected array $allAges = [];
    protected array $allPublishingHouses = [];
    protected array $allSeries = [];

    protected array $allPersons = [];

    protected const RUSSIAN_ALPHABET = [
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р',
        'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
    ];

    public function __construct()
    {
        $this->db = Yii::$app->get('db2');
        $this->dbSite = Yii::$app->get('db');

        parent::__construct();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPricesAndQuantityFormated(): array
    {
        $data = $this->getPricesAndQuantity();
        $products = $this->dbSite->createCommand("
                SELECT
                    labirint_id AS id,
                    quantity,
                    price,
                    id as deltaId
                FROM products
                WHERE labirint_id IS NOT NULL AND price > 0
            ")
            ->queryAll();

        $updateArray = [];
        foreach ($products as ['id' => $id, 'quantity' => $quantity, 'price' => $price, 'deltaId' => $deltaId]) {
            if (
                isset($data[$id]) &&
                (
                    $data[$id]['price'] != $price ||
                    $data[$id]['quantity'] != $quantity
                )
            ) {
                $updateArray[$id] = $data[$id];
                $updateArray[$id]['deltaId'] = $deltaId;
            }

            if (!isset($data[$id]) && $quantity > 0) {
                $updateArray[$id] = [
                    'labirintId' => $id,
                    'quantity' => 0
                ];
            }
        }

        return $updateArray;
    }

    /**
     * @return array
     */
    public function getPricesAndQuantity(): array
    {
        $output = [];
        try {
            $rows = $this->db->createCommand("
                    SELECT
                        a.id_books as labirintId,
                        a.price_db as price,
                        a.quantity
                    FROM viewBooksForMyShop AS a WITH(NOLOCK)
                    JOIN viewBooksInfoForMyShop AS b WITH(NOLOCK)
                            ON a.id_books = b.id
                    WHERE ISNULL(price_db, 0) > 0 AND (typeVolume NOT IN (1,2) OR typeVolume IS NULL)
                ")
                ->queryAll();

            foreach ($rows as $row) {
                $output[$row['labirintId']] = $row;
            }
        } catch (\Exception $e) {
            var_dump('Ошибка с запросом - ' . $e->getMessage());
        }

        return $output;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $outputProducts = [];
        $outputSeries = [];
        $outputPersons = [];
        $outputPubhouses = [];

        print_r('getData start' . PHP_EOL);
        try {
            // Получение товаров из OBMB
            print_r('getProducts rows get start' . PHP_EOL);
            $rows = $this->db->createCommand("
                    SELECT
                        *
                    FROM viewBooksInfoForMyShop AS a WITH(NOLOCK)
                        JOIN viewBooksForMyShop AS b WITH(NOLOCK)
                            ON a.id = b.id_books
                                AND ISNULL(b.price_db, 0) > 0
                                AND (typeVolume NOT IN (1,2) OR typeVolume IS NULL)
                ")
                ->queryAll();
            print_r('getProducts rows get end' . PHP_EOL);

            // Сетим уровни книги
            print_r('getAllLevels start' . PHP_EOL);
            $this->getAllLevels();
            print_r('getAllLevels end' . PHP_EOL);

            // Сетим существующие возрастные категории на дельте
            print_r('getAllAges start' . PHP_EOL);
            $this->getAllAges();
            print_r('getAllAges end' . PHP_EOL);

            // Сетим существующие издательства на дельте
            print_r('getAllPublishingHouses start' . PHP_EOL);
            $this->getAllPublishingHouses();
            print_r('getAllPublishingHouses end' . PHP_EOL);

            // Сетим существующих авторов на дельте
            print_r('getAllPersons start' . PHP_EOL);
            $this->getAllPersons();
            print_r('getAllPersons end' . PHP_EOL);

            // Сетим существующие серии на дельте
            print_r('getAllSeries start' . PHP_EOL);
            $this->getAllSeries();
            print_r('getAllSeries end' . PHP_EOL);

            // Получаем id товаров дельты
            print_r('getProducts productsId get start' . PHP_EOL);
            $productsId = $this->dbSite->createCommand("
                SELECT
                    labirint_id AS id
                FROM products
                WHERE labirint_id IS NOT NULL
            ")
                ->queryColumn();
            print_r('getProducts productsId get end' . PHP_EOL);

            print_r('getProducts productsIsbn get start' . PHP_EOL);
            $productsIsbn = $this->getProductsIsbn();
            print_r('getProducts productsIsbn get end' . PHP_EOL);

            // Получаем инфу по сериям дельты
            print_r('getSeries ids get start' . PHP_EOL);
            $seriesInfo = $this->getSeriesInfo();
            print_r('getSeries ids get end' . PHP_EOL);

            // Получаем инфу по авторам дельты
            $persons = $this->dbSite->createCommand("
                SELECT
                    labirint_id
                FROM persons
                WHERE labirint_id IS NOT NULL
            ")->queryColumn();

            // Получаем инфу по издательствам дельты
            print_r('getPublishingHouses ids get start' . PHP_EOL);
            $pubhouseInfo = $this->getPubhousesInfo();
            print_r('getPublishingHouses ids get end' . PHP_EOL);

            $preparedAuthors = [];
            print_r('getData foreach start' . PHP_EOL);
            foreach ($rows as $row) {

                // Обновляем товары
                $outputProducts[] = $this->getProducts($row, $productsId, $productsIsbn);

                // Обновляем серии
                $outputSeries[] = $this->getSeries($row, $seriesInfo);

                // Подготавливаем авторов для обновления
                $authors = $row['authors'] ? $this->getAuthors($row['authors']) : [];
                foreach ($authors as $author) {
                    $preparedAuthors[$author['id']] = $author['name'];
                }

                // Обновляем издателсьтва
                $outputPubhouses[] = $this->getPubhouses($row['pubhouse.id'], $row['pubhouse.name'], $pubhouseInfo);

            }
            print_r('getData foreach end' . PHP_EOL);

            print_r('getAuthors foreach start' . PHP_EOL);

            // Обновляем авторов
            foreach ($preparedAuthors as $key => $res) {
                if (!in_array($key, $persons)) {
                    $outputPersons[] = [
                        $this->isRussianPerson($res) ? 'nameFullRu' : 'nameFull' => $res,
                        'labirintId' => $key
                    ];
                }
            }
            print_r('getAuthors foreach end' . PHP_EOL);
        } catch (\Exception $e) {
            print_r('Ошибка с запросом - ' . $e->getMessage() . PHP_EOL);
        }
        print_r('getData end' . PHP_EOL);

        return [
            'products' => $outputProducts,
            'series' => $outputSeries,
            'persons' => $outputPersons,
            'pubhouses' => $outputPubhouses
        ];
    }

    /**
     * @param string $isbn
     * @return string
     */
    private function simpleIsbn(string $isbn): string
    {
        return preg_replace('/[^0-9]/', '', $isbn);
    }

    /**
     * @return void
     */
    private function getAllLevels(): void
    {
        foreach ((new LevelRepository())->getLevels() as $level) {
            $this->allLevels[$level['id']] = $level['name'];
        }
    }

    /**
     * @return void
     */
    private function getAllAges(): void
    {
        foreach ((new AgeRepository())->getAges() as $ageCategory) {
            $this->allAges[$ageCategory['id']] = $ageCategory['intName'];
        }
    }

    /**
     * @param string $field
     * @return string
     */
    private function getProductAuthors(string $field): string
    {
        if ($field) {
            $authors = \json_decode($field, true);
            $authors = array_filter($authors, function ($authors) {
                return $authors['type'] == 'Автор';
            });
            return implode(
                ', ',
                array_column($authors, 'name'));
        }
        return '';
    }

    /**
     * @param string $field
     * @return array
     */
    private function getProductPersons(string $field): array
    {
        $ids = [];
        if ($personIds = $this->getAuthors($field, true)) {
            foreach ($personIds as $personId) {
                if ($key = array_search($personId, $this->allPersons)) {
                    $ids[] = $key;
                }
            }
        }

        return $ids;
    }

    /**
     * @param string $field
     * @return array
     */
    private function getProductLanguages(string $field): array
    {
        return array_column($field ? \json_decode($field, true) : [], 'ID');
    }

    /**
     * @return void
     */
    private function getAllPublishingHouses(): void
    {
        $publishingHouseRep = new PublishingHouseRepository();
        $publishingHouseRep->setParams(['withPagination' => false]);
        foreach ($publishingHouseRep->getPublishingHouses()['publishers'] as $publishingHouse) {
            $this->allPublishingHouses[$publishingHouse['id']] = $publishingHouse['labirintId'];
        }
    }

    private function getAllSeries(): void
    {
        foreach ((new SeriesRepository())->getSeries() as $series) {
            $this->allSeries[$series['id']] = $series['labirintId'];
        }
    }

    /**
     * @return void
     */
    private function getAllPersons(): void
    {
        foreach ((new PersonRepository())->getPersons()['persons'] as $person) {
            $this->allPersons[$person['id']] = $person['labirintId'];
        }
    }


    /**
     * @param string $field
     * @param bool $onlyId
     * @return array
     */
    private function getAuthors(string $field, bool $onlyId = false): array
    {
        $authors = \json_decode($field, true);

        if ($onlyId) {
            $authors =  array_filter($authors, function ($authors) {
                if (isset($authors['id']) && isset($authors['name'])) {
                    return $authors['type'] == 'Автор';
                }
                return [];
            });

            return array_column($authors, 'id');
        }

        return array_filter($authors, function ($authors){
            if (isset($authors['id']) && isset($authors['name'])) {
                return $authors['type'] == 'Автор';
            }
        });
    }

    /**
     * @param string $data
     * @return bool
     */
    private function isRussianPerson(string $data): bool
    {
        return in_array(mb_substr(mb_strtolower(ltrim($data)), 0, 1), self::RUSSIAN_ALPHABET);
    }


    /**
     * @return array
     * @throws Exception
     */
    private function getProductsIsbn(): array
    {
        $productsIsbn = [];
        $productsData = $this->dbSite->createCommand("
                SELECT
                    id,
                    isbn
                FROM products
                WHERE isbn IS NOT NULL
                    AND labirint_id IS NULL
            ")
            ->queryAll();

        foreach ($productsData as $item) {
            $productsIsbn[$this->simpleIsbn($item['isbn'])] = $item['id'];
        }
        unset($productsData);

        return $productsIsbn;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getSeriesInfo(): array
    {
        $series = $this->dbSite->createCommand("
                SELECT
                    id,
                    name,
                    labirint_id
                FROM series
            ")
            ->queryAll();
        $seriesNames = [];
        $seriesIds = [];
        foreach ($series as $ser) {
            $seriesNames[$ser['id']] = $ser['name'];
            if (!$ser['labirint_id']) {
                continue;
            }
            $seriesIds[$ser['id']] = $ser['labirint_id'];
        }

        return [
            'seriesIds' => $seriesIds,
            'seriesNames' => $seriesNames
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getPubhousesInfo(): array
    {
        $publishingHouses= $this->dbSite->createCommand("
                SELECT
                    id,
                    name,
                    labirint_id
                FROM publishing_houses
            ")
            ->queryAll();
        $pubhousesNames = [];
        $pubhousesIds = [];
        foreach ($publishingHouses as $pubHouses) {
            $pubhousesNames[$pubHouses['id']] = $pubHouses['name'];
            if (!$pubHouses['labirint_id']) {
                continue;
            }
            $pubhousesIds[$pubHouses['id']] = $pubHouses['labirint_id'];
        }

        return [
            'pubhouseIds' => $pubhousesIds,
            'pubhouseNames' => $pubhousesNames
        ];
    }

    /**
     * @param array $row
     * @param array $productsId
     * @param array $productsIsbn
     * @return array
     */
    private function getProducts(array $row, array $productsId, array $productsIsbn): array
    {
        $idAge = array_search($row['forAge'], $this->allAges);
        if (in_array($row['id'], $productsId)) {
            return [
                'labirintId' => $row['id'],
                'quantity' => $row['quantity'],
                'price' => $row['price_db'] ?: '',
                'publishingYear' => $row['yearPub'] ?: '',
                'pageMaterial' => $row['pageType'] ?: '',
                'pagesNumber' => $row['pages'] ?: '',
                'pdf' => $row['pdf'] ?: '',
                'weight' => $row['weight'] ?: '',
                'size' => $row['size'] ?: '',
                'color' => $row['color'] ?: '',
                'ages' => $idAge ? (array)$idAge : '',
                'authors' => $row['authors'] ? $this->getProductAuthors($row['authors']) : '',
                'publishingHouseId' => array_search($row['pubhouse.id'], $this->allPublishingHouses) ?: '',
                'bindingMaterial' => $row['cover'] ?? '',
                'persons' => $row['authors'] ? $this->getProductPersons($row['authors']) : '',
                'seriesId' => array_search($row['series.id'], $this->allSeries) ?: '',
                'nds' => $row['nds'],
            ];
        } elseif (key_exists($this->simpleIsbn($row['ISBN']), $productsIsbn)) {
            return [
                'id' => $productsIsbn[$this->simpleIsbn($row['ISBN'])],
                'labirintId' => $row['id'],
                'quantity' => $row['quantity'],
                'price' => $row['price_db'] ?: '',
                'publishingYear' => $row['yearPub'] ?: '',
                'pageMaterial' => $row['pageType'] ?: '',
                'pagesNumber' => $row['pages'] ?: '',
                'pdf' => $row['pdf'] ?: '',
                'weight' => $row['weight'] ?: '',
                'size' => $row['size'] ?: '',
                'color' => $row['color'] ?: '',
                'ages' => $idAge ? (array)$idAge : '',
                'authors' => $row['authors'] ? $this->getProductAuthors($row['authors']) : '',
                'publishingHouseId' => array_search($row['pubhouse.id'], $this->allPublishingHouses) ?: '',
                'bindingMaterial' => $row['cover'] ?? '',
                'persons' => $row['authors'] ? $this->getProductPersons($row['authors']) : '',
                'seriesId' => array_search($row['series.id'], $this->allSeries) ?: '',
                'nds' => $row['nds'],
            ];
        } else {
            return [
                'labirintId' => $row['id'],
                'title' => $row['name'],
                'quantity' => $row['quantity'],
                'isbn' => $row['ISBN'] ?: '',
                'price' => $row['price_db'] ?: '',
                'publishingYear' => $row['yearPub'] ?: '',
                'pageMaterial' => $row['pageType'] ?: '',
                'pagesNumber' => $row['pages'] ?: '',
                'annotation' => $row['description'] ?: '',
                'cover' => $row['img'] ?: '',
                'pdf' => $row['pdf'] ?: '',
                'active' => !($row['isHidden'] && $row['is_blacklist']),
                'weight' => $row['weight'] ?: '',
                'size' => $row['size'] ?: '',
                'color' => $row['color'] ?: '',
                'authors' => $row['authors'] ? $this->getProductAuthors($row['authors']) : '',
                'languages' => $row['languagesInfo'] ? $this->getProductLanguages($row['languagesInfo']) : [],
                'ages' => $idAge ? (array)$idAge : '',
                'publishingHouseId' => array_search($row['pubhouse.id'], $this->allPublishingHouses) ?: '',
                'bindingMaterial' => $row['cover'] ?? '',
                'persons' => $row['authors'] ? $this->getProductPersons($row['authors']) : '',
                'seriesId' => array_search($row['series.id'], $this->allSeries) ?: '',
                'nds' => $row['nds'],
            ];
        }
    }

    /**
     * @param array $row
     * @param array $seriesInfo
     * @return array
     */
    private function getSeries(array $row, array $seriesInfo): array
    {
        if ($keySeries = array_search($row['series.id'], $seriesInfo['seriesIds'])) {
            return [
                'id' => $keySeries,
                'name' => $row['series.name'],
                'publishingHouseId' => array_search($row['pubhouse.id'], $this->allPublishingHouses) ?: '',
            ];
        } elseif ($kSeries = array_search($row['series.name'], $seriesInfo['seriesNames'])) {
            return [
                'id' => $kSeries,
                'labirintId' => $row['series.id'],
                'publishingHouseId' => array_search($row['pubhouse.id'], $this->allPublishingHouses) ?: '',
            ];
        } else {
            return [
                'name' => $row['series.name'],
                'labirintId' => $row['series.id'],
                'publishingHouseId' => array_search($row['pubhouse.id'], $this->allPublishingHouses) ?: '',
            ];
        }
    }

    private function getPubhouses(int $pubhouseId, string $pubhouseName, array $pubhouseInfo): array
    {
        if ($keyPubhouse = array_search($pubhouseId, $pubhouseInfo['pubhouseIds'])) {
            return [
                'id' => $keyPubhouse,
                'name' => $pubhouseName,
                'dateUpdated' => date('Y-m-d H:i:s', strtotime('now')) // почему-то date_updated не меняется при обновлении данных
            ];
        } elseif ($kPubhouse = array_search($pubhouseName, $pubhouseInfo['pubhouseNames'])) {
            return [
                'id' => $kPubhouse,
                'labirintId' => $pubhouseId,
                'dateUpdated' => date('Y-m-d H:i:s', strtotime('now')) // аналогично
            ];
        } else {
            return [
                'name' => $pubhouseName,
                'labirintId' => $pubhouseId
            ];
        }
    }
}