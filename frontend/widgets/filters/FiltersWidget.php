<?php

namespace frontend\widgets\filters;

use common\models\age\AgeService;
use common\models\genre\GenreDTO;
use common\models\genre\GenreService;
use common\models\level\LevelService;
use common\models\product\ProductEntity;
use common\models\product\ProductService;
use common\models\publishing_house\PublishingHouseDTO;
use common\models\publishing_house\PublishingHouseService;
use common\models\search\index\ProductIndex;
use common\models\search\ManticoreSearchService;
use yii\base\Widget;

class FiltersWidget extends Widget
{
    const FILTERS_TYPE_PRODUCTS = 'products';
    const FILTERS_TYPE_COURSES = 'courses';
    const FILTERS_TYPE_SEARCH = 'search';

    const FILTER_TYPE_CHECKBOXES = 'checkboxes';
    const FILTER_TYPE_ALPHABET = 'alphabet';

    const FILTER_FIELDS = [
        self::FILTERS_TYPE_PRODUCTS => [
            [
                'name' => 'Levels',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'levels',
            ],
            [
                'name' => 'Ages',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'ages',
            ],
            [
                'name' => 'Genres',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'genres',
            ],
            [
                'name' => 'PublishingHouse',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'publishingHouseId',
            ],
        ],
        self::FILTERS_TYPE_COURSES => [
            [
                'name' => 'Alphabet',
                'type' => self::FILTER_TYPE_ALPHABET,
                'paramName' => 'words',
            ],
            [
                'name' => 'ChildrenGenres',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'parentId',
            ],
        ],
        self::FILTERS_TYPE_SEARCH => [
            [
                'name' => 'Levels',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'levels',
            ],
            [
                'name' => 'Ages',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'ages',
            ],
            [
                'name' => 'Genres',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'genres',
            ],
            [
                'name' => 'PublishingHouse',
                'type' => self::FILTER_TYPE_CHECKBOXES,
                'paramName' => 'publishingHouseId',
            ],
        ],
    ];

    public string $type;
    public array $params = [];
    public ?int $genreId;

    protected ManticoreSearchService $searchService;

    public function init()
    {
        $this->searchService = new ManticoreSearchService();
        FiltersAsset::register($this->view);
        parent::init();
    }

    public function renderOpenButton(): string
    {
        return $this->render('open-button');
    }

    public function renderIndex(): string
    {
        $filters = [];

        foreach (self::FILTER_FIELDS[$this->type] as $data) {
            $method = "get{$data['name']}Filter";

            if (method_exists($this, $method)) {
                if ($filter = $this->$method(...$data)) {
                    $filters[] = $filter;
                }
            }
        }

        return $this->render('index', ['filters' => $filters]);
    }

    private function getLevelsFilter(string $name, string $type, string $paramName): array
    {
        $service = new LevelService();

        return [
            'title' => 'Уровень',
            'list' => (array)$service->getLevelsByCatalog(),
            'template' => "fields/$type",
            'value' => $this->values[$paramName] ?? null,
            'paramName' => $paramName,
        ];
    }

    private function getAgesFilter(string $name, string $type, string $paramName): array
    {
        $service = new AgeService();

        return [
            'title' => 'Возраст',
            'list' => (array)$service->getAgesByCatalog(),
            'template' => "fields/$type",
            'value' => $this->values[$paramName] ?? null,
            'paramName' => $paramName,
        ];
    }

    private function getGenresFilter(string $name, string $type, string $paramName): ?array
    {
        $service = new GenreService();
        $service->setOrder(['name' => 'asc']);
        if (isset($this->params['search'])) {
            $this->searchService->setClient('client', new ProductIndex());
            $this->searchService->setParams(['search' => $this->params['search'], 'active' => 1, 'is_new' => 0]);

            $ids = $this->searchService->getIdsForSearch();

            if ($ids) {
                foreach (
                    ProductEntity::find()
                        ->select(['genres.*'])
                        ->where(['active' => 1])
                        ->join(
                            'JOIN',
                            'product_genres',
                            "product_genres.product_id = products.id"
                        )
                        ->join(
                            'JOIN',
                            'genres',
                            "product_genres.genre_id = genres.id"
                        )
                        ->andWhere(['OR',
                            "products.id IN (" . implode(',', $ids) . ")"
                        ])
                        ->asArray()
                        ->all() as $genre) {
                    $formatGenres[$genre['id']] = GenreDTO::make($genre);
                }
            }
        } else {
            $service->setParams(['level' => [3], 'parentId' => $this->genreId ?? 1]);
            $formatGenres = $service->getGenresByCatalog();
        }

        if (empty($formatGenres)) {
            return null;
        }

        return [
            'title' => 'Жанр',
            'list' => (array)$formatGenres,
            'template' => "fields/$type",
            'value' => $this->values[$paramName] ?? null,
            'paramName' => $paramName,
        ];
    }

    private function getChildrenGenresFilter(string $name, string $type, string $paramName): ?array
    {
        $service = new GenreService();
        $service->setOrder(['name' => 'asc']);
        $service->setParams(['level' => [3], 'parentId' => $this->genreId ?? 1]);
        $formatGenres = $service->getGenresByCatalog();

        if (empty($formatGenres)) {
            return null;
        }

        return [
            'title' => 'Категория',
            'list' => (array)$formatGenres,
            'template' => "fields/$type",
            'value' => $this->values[$paramName] ?? null,
            'paramName' => $paramName,
        ];
    }

    private function getPublishingHouseFilter(string $name, string $type, string $paramName): array
    {
        $publishingHouses = [];
        if (isset($this->params['search'])) {
            $this->searchService->setClient('client', new ProductIndex());
            $this->searchService->setParams(['search' => $this->params['search'], 'active' => 1, 'is_new' => 0]);

            $ids = $this->searchService->getIdsForSearch();
            if ($ids) {
                foreach (ProductEntity::find()
                             ->where(['active' => 1])
                             ->select(['products.publishing_house_id AS id', 'publishing_houses.name'])
                             ->join(
                                 'JOIN',
                                 'publishing_houses',
                                 "publishing_houses.id = products.publishing_house_id"
                             )
                             ->andWhere(['OR',
                                 "products.id IN (" . implode(',', $ids) . ")"
                             ])
                             ->asArray()
                             ->all() as $publishingHouse) {
                    $publishingHouses[$publishingHouse['id']] = PublishingHouseDTO::make($publishingHouse);
                }
            }
        } else {
            foreach (ProductEntity::find()
                 ->where(['active' => 1])
                 ->select(['products.publishing_house_id AS id', 'publishing_houses.name'])
                 ->join(
                     'JOIN',
                     'product_genres',
                     "product_genres.genre_id IN ({$this->genreId}) AND product_genres.product_id = products.id"
                 )
                 ->join(
                     'JOIN',
                     'publishing_houses',
                     "publishing_houses.id = products.publishing_house_id"
                 )
                 ->orderBy('publishing_houses.name ASC')
                 ->asArray()
                 ->all() as $publishingHouse) {
                $publishingHouses[$publishingHouse['id']] = PublishingHouseDTO::make($publishingHouse);
            }
        }

        return [
            'title' => 'Издатель',
            'list' => $publishingHouses,
            'template' => "fields/$type",
            'value' => $this->values[$paramName] ?? [],
            'paramName' => $paramName,
        ];
    }

    private function getAlphabetFilter(string $name, string $type, string $paramName): array
    {
        return [
            'title' => 'По алфавиту',
            'list' => (array)$this->getAlphabet(),
            'template' => "fields/$type",
            'value' => $this->values[$paramName] ?? [],
            'paramName' => $paramName,
        ];
    }

    private function getAlphabet(): array
    {
        return [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];
    }
}