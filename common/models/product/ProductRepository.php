<?php

namespace common\models\product;

use common\models\AbstractRepository;
use common\models\age\AgeRepository;
use common\models\cart\CartRepository;
use common\models\cart\CartService;
use common\models\favorite\FavoriteRepository;
use common\models\genre\GenreRepository;
use common\models\level\LevelRepository;
use common\models\person\PersonRepository;
use common\models\rating\RatingRepository;
use common\models\publishing_house\PublishingHouseRepository;
use common\models\language\LanguageRepository;
use common\models\Pagination;
use common\models\series\SeriesRepository;
use common\models\user\UserService;
use yii\db\ActiveQuery;
use yii\db\Exception;

class ProductRepository extends AbstractRepository
{
    protected PublishingHouseRepository $publishingHouseRepository;
    protected GenreRepository $genreRepository;
    protected AgeRepository $ageRepository;
    protected RatingRepository $ratingRepository;
    protected LanguageRepository $languageRepository;
    protected FavoriteRepository $favoriteRepository;
    protected PersonRepository $personRepository;
    protected LevelRepository $levelRepository;
    protected SeriesRepository $seriesRepository;
    protected Pagination $pagination;
    protected array $orders = [
        "IF(products.quantity = 0, products.`quantity` + 1, ISNULL(products.quantity))" => 'ASC',
        'IF(price = 0, `price` + 1, ISNULL(price))' => 'ASC',
        'ISNULL(cover)' => 'ASC',
        'id' => 'DESC'
    ];

    protected string $defaultOrders = '
        IF(products.quantity = 0, products.`quantity` + 1, ISNULL(products.quantity)),
        IF(price = 0, `price` + 1, ISNULL(price)),
        ISNULL(cover)
        ';

    protected array $fieldsMap = [
        'publishing_house_id' => 'publishingHouseId',
        'labirint_id' => 'labirintId',
        'publishing_year' => 'publishingYear',
        'page_material' => 'pageMaterial',
        'binding_material' => 'bindingMaterial',
        'pages_number' => 'pagesNumber',
        'level_id' => 'levelId',
        'is_new' => 'isNew',
        'volumes_count' => 'volumesCount',
        'is_popular' => 'isPopular',
        'short_annotation' => 'shortAnnotation',
        'series_id' => 'seriesId',
    ];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];
    protected array $availableOrders = [
        'id',
        'date_created',
        'date_updated',
        'price',
        'ISNULL(cover)',
        'IF(price = 0, `price` + 1, ISNULL(price))',
        "IF(products.quantity = 0, products.`quantity` + 1, ISNULL(products.quantity))",
        'title'
    ];

    public function __construct()
    {
        $this->entity = new ProductEntity();
        $this->publishingHouseRepository = new PublishingHouseRepository();
        $this->genreRepository = new GenreRepository();
        $this->ageRepository = new AgeRepository();
        $this->ratingRepository = new RatingRepository();
        $this->languageRepository = new LanguageRepository();
        $this->levelRepository = new LevelRepository();
        $this->favoriteRepository = new FavoriteRepository();
        $this->personRepository = new PersonRepository();
        $this->seriesRepository = new SeriesRepository();
        $this->pagination = Pagination::getInstance();

        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'ProductSimpleSearch',
            'ProductGenres',
            'ProductLevels',
            'ProductAges',
            'Simple' => [
                'params' => [
                    'id', 'labirint_id', 'price', 'is_new', 'isbn', 'is_popular',
                    'course_id', 'active', 'publishing_house_id', 'cover', 'series_id'
                ]
            ],
            'ExcludeSimple' => [
                'params' => ['id']
            ],
            'ProductInStock'
        ]);
    }

    public function getProducts(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());
        $query = $this->setOrderByInQuery($query);
        $output = [];

        if ($this->limit > 0) {
            $query->limit($this->limit);
        }elseif ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
            $output['pagination'] = $this->pagination->getData();
        }

        $products = $query
            ->asArray()
            ->all();

        $favorites = [];
        $cart = [];
        if (isset(\Yii::$app->user) && !\Yii::$app->user->isAdmin()) {

            if ($userId = \Yii::$app->user->getId()) {
                $this->favoriteRepository->setParams([
                    'userId' =>  $userId,
                ]);
            } else {
                $this->favoriteRepository->setParams([
                    'sessionKey' =>  UserService::getCurrentSessionKey(),
                ]);
            }

            $favorites = $this->favoriteRepository->getFavorite();

            $cartService = new CartService();
            $cart = $cartService->getCartItems();
        }

        foreach ($products as &$product) {
            $product = $this->convertField($product);
            $product['genres'] = $this->getGenres($product['id']);
            $product['publishingHouse'] = $product['publishingHouseId'] ?
                $this->getPublishingHouseName($product['publishingHouseId']) : '';
            $product['ages'] = $this->getAges($product['id']);
            $this->setRating($product);
            $product['languages'] = $this->getLanguages($product['id']);
            $product['levels'] = $this->getLevels($product['id']);
            $product['isFavourite'] = in_array($product['id'], $favorites);
            $product['isCart'] = false;
            $product['persons'] = $this->getPersons($product['id']);
            $product['seriesName'] = $product['seriesId'] ? $this->getSeriesName($product['seriesId']) : '';

            if (array_key_exists($product['id'], $cart)) {
                $product['isCart'] = true;
                $product['cartItemId'] = $cart[$product['id']]['id'];
                $product['cartQuantity'] = $cart[$product['id']]['quantity'];
            }
        }

        $output['products'] = $products;

        return $output;
    }

    public function create(): array
    {
        $this->entity->setScenario(self::SCENARIO_CREATE);
        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $this->entity->load($this->convertField($this->params, true), '');

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new Exception('Создание не удалось');
            }

            // Связанные поля
            $this->saveRelatedFields($this->entity->id, $connection);

            $transaction->commit();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
            $transaction->rollBack();
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    public function update(int $id): array
    {
        try {
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $this->entity = ProductEntity::findOne($id);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate() || !$this->entity->save()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new Exception('Обновление не удалось');
            }

            // Связанные поля
            $this->saveRelatedFields($id, $connection);

            $transaction->commit();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
            $transaction->rollBack();
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    public function delete()
    {
        $this->entity->setScenario(self::SCENARIO_DELETE);
    }

    public function checkIsExist(): bool|int
    {
        $id = $this->setParamsInQuery($this->entity::find())
            ->select('id')
            ->scalar();

        if ($id) {
            return $id;
        }
        return false;
    }

    //region relative data
    public function getPublishingHouseName(int $id): string
    {
        $this->publishingHouseRepository->setParams(['id' => $id]);

        return (string) $this->publishingHouseRepository->getPublishingHouses()['publishers'][0]['name'] ?? '';
    }

    private function getGenres(int $id): array
    {
        $this->genreRepository->setParams(['productId' => $id]);

        return $this->genreRepository->getGenres();
    }

    public function getAges(int $id): array
    {
        $this->ageRepository->setParams(['productId' => $id]);

        return $this->ageRepository->getAges();
    }


    public function getLanguages(int $id): array
    {
        $this->languageRepository->setParams(['productId' => $id]);
        return $this->languageRepository->getLanguages();
    }


    public function getLevels(int $id): array
    {
        $this->levelRepository->setParams(['productId' => $id]);
        return $this->levelRepository->getLevels();
    }

    /**
     * @param int $id
     * @return string
     */
    private function getSeriesName(int $id): string
    {
        $this->seriesRepository->setParams(['id' => $id]);
        return $this->seriesRepository->getSeries()[0]['name'];
    }


    /**
     * @param int $id
     * @return array
     */
    private function getPersons(int $id): array
    {
        $this->personRepository->setParams(['productId' => $id]);
        $persons = $this->personRepository->getPersons()['persons'];
        foreach($persons as &$person) {
            $person['url'] = "<a class='mini-card__person-link' href='/person/{$person['id']}'>{$person['name']}</a>";
        }

        return $persons;
    }
    //endregion

    /**
     * @throws Exception
     */
    private function saveRelatedFields(int $productId, &$connection): void
    {
        if (isset($this->params['genres']) && $this->params['genres']) {
            $this->saveRelatedField('genre', $this->params['genres'], $productId, $connection);
        }

        if (isset($this->params['languages']) && $this->params['languages']) {
            $this->saveRelatedField('language', $this->params['languages'], $productId, $connection);
        }

        if (isset($this->params['levels']) && $this->params['levels']) {
            $this->saveRelatedField('level', $this->params['levels'], $productId, $connection);
        }

        if (isset($this->params['ages']) && $this->params['ages']) {
            $this->saveRelatedField('age', $this->params['ages'], $productId, $connection);
        }

        if (isset($this->params['persons']) && $this->params['persons']) {
            $this->saveRelatedField('person', $this->params['persons'], $productId, $connection);
        }
    }

    /**
     * @throws Exception
     */
    private function saveRelatedField(string $name, array $data, int $productId, &$connection): void
    {
        $repositoryName = "{$name}Repository";
        $methodName = 'get' . ucfirst($name) . 's';

        $this->$repositoryName->setParams(['ids' => $data]);

        // TODO: костыль временный, нужно будет исправить!
        if ($name == 'person') {
            $ids = array_column($this->$repositoryName->$methodName()['persons'], 'id');
        } else {
            $ids = array_column($this->$repositoryName->$methodName(), 'id');
        }


        if (empty($ids)) {
            throw new Exception("Неверные id $name");
        }

        $connection->createCommand("
                DELETE FROM product_{$name}s WHERE product_id = $productId
            ")->execute();

        $connection->createCommand("
                INSERT INTO product_{$name}s (product_id, {$name}_id, date_created, date_updated) 
                {$this->getRelatedValuesInsertString($productId, $ids)}
            ")->execute();
    }

    private function getRelatedValuesInsertString(int $productId, array $relatedIds): string
    {
        $values = [];
        foreach ($relatedIds as $relatedId) {
            $values[] = "({$productId}, {$relatedId}, current_date(), current_date())";
        }
        return "VALUES " . implode(', ', $values) ;
    }

    private function setRating(array &$product): void
    {
        $this->ratingRepository->setParams(['productId' => $product['id']]);
        $rating = $this->ratingRepository->getRatingByProduct();
        $product['rating'] = $rating['value'];
        $product['voteCount'] = $rating['count'];
    }

    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    public function getProductByGenre(array $ids): array
    {
        $genresString = implode(',', $ids);

        return $this->entity::getDb()->createCommand("
            SELECT p.*, MAX(a.intName) as intName, ph.name as pubName
            FROM products p
                JOIN product_genres pg ON pg.genre_id IN ({$genresString}) AND pg.product_id = p.id
                LEFT JOIN product_ages pa ON pa.product_id  =  p.id
                LEFT JOIN ages a ON pa.age_id  =  a.id
                LEFT JOIN publishing_houses ph ON ph.id  =  p.publishing_house_id
                WHERE p.active = 1 AND p.quantity > 0 AND p.annotation != '' AND p.cover !=''
            GROUP BY p.id
        ")->queryAll();
    }


    /**
     * @param int $page
     * @return array
     */
    public function getProductsForManticoreSearch(int $page): array
    {
        $query = $this->entity::find();

        $this->pagination->setTotalCount($query->count());
        $this->pagination->setCurrentPage($page);
        $this->pagination->setCountOnPage(1000);
        $query = $this->setOffsetLimit($query);
        $output['pagination'] = $this->pagination->getData();

        $products = $query
            ->asArray()
            ->all();

        foreach ($products as &$product) {
            $product = $this->convertField($product);
        }

        $output['products'] = $products;

        return $output;
    }


    /**
     * @return array
     * @throws Exception
     */
    public function getCoverProducts(): array
    {
        return $this->entity::getDb()->createCommand("
                SELECT * 
                FROM products
                WHERE cover LIKE '%labirint.ru%' OR (cover = '' OR cover IS NULL) AND labirint_id IS NOT NULL
        ")->queryAll();
    }


    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    public function getProductsWithLabId(array $ids): array
    {
        $andWhere = '';

        if ($ids) $andWhere = " AND id NOT IN (" . implode(',', $ids) . ")";

        return $this->entity::getDb()->createCommand("
                SELECT * 
                FROM products
                WHERE labirint_id IS NOT NULL $andWhere
        ")->queryAll();
    }
}