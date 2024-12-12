<?php

namespace common\models\feed;

use common\models\genre\GenreService;
use yii\db\Exception;

class FeedBuilder
{
    protected string $path = '/var/www/deltabook/current/frontend/web/feeds/';
    protected FeedService $feedService;
    protected GenreService $genreService;
    protected string $params;
    protected array $categoryIds = [];
    protected const IDS_EDUCATIONAL_LITERATURE =
        [
            270,
            333,
            361,
            385,
            409,
            700,
            912,
            914,
            990,
            1045,
            1569,
            2298,
            2299,
            2300,
            2301,
            2302,
            2303,
            2318,
            2320,
            2325,
            2346,
            2322,
            2324,
            2326
        ];
    protected const IDS_BOOKS_FOR_READ = [163, 346, 383, 407, 1007, 2128];
    protected const IDS_BOOKS_OTHERS_LANGUAGES = [2298, 2299, 2300, 2301, 2302, 2303, 2318, 2320, 2322, 2325, 2346];

    public function __construct()
    {
        $this->feedService = new FeedService();
        $this->genreService = new GenreService();
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $file = $this->path . $this->params . ".xml";
        $fp = fopen($file, "w");
        print_r('Создание фида ' . $this->params . ".xml" . PHP_EOL);
        $str = $this->createXml();
        print_r('Запись фида ' . $this->params . ".xml" . PHP_EOL);
        fwrite($fp, $str);
        fclose($fp);
        print_r('Архивирование фида ' . $this->params . ".xml" . PHP_EOL);
        $this->createGz();
//        print_r('Удаление фида ' . $this->params . ".xml" . PHP_EOL);
//        $this->deleteXml();
    }

    /**
     * @return string
     */
    protected function createXml(): string
    {
        $header = $this->getHeader();
        $categories = $this->createCategories();
        $offers = $this->createOffers();
        $footer = $this->getFooter();
        return $header . $categories . $offers . $footer;
    }

    /**
     * @return void
     */
    protected function createGz(): void
    {
        $fileGz = $this->path . $this->params;
        $fileInGz = $this->path . $this->params . ".xml";
        shell_exec('gzip -c "' . $fileInGz . '" > "' . $fileGz . '.gz"');
    }

    /**
     * @return void
     */
    protected function deleteXml(): void
    {
        $file = $this->path . $this->params . ".xml";
        shell_exec('rm ' . $file);
    }


    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    protected function getProductsInfoByGenres(array $ids): array
    {
        return $this->feedService->getProductByGenres($ids);
    }


    /**
     * @return array
     * @throws Exception
     */
    protected function getGenresIdsList(): array
    {
        $genres = match ($this->params) {
            'edu' => self::IDS_EDUCATIONAL_LITERATURE,
            'read' => self::IDS_BOOKS_FOR_READ,
            'others' => self::IDS_BOOKS_OTHERS_LANGUAGES
        };

        $this->genreService->setParams(['id' => $genres]);
        $genresParams = $this->genreService->getGenres();
        $upperGenreIds = [];
        foreach ($genresParams as $genre) {
            if ($genre['parentId'] && !in_array($genre['parentId'], $upperGenreIds)) {
                $upperGenreIds[] = $genre['parentId'];
            }
        }

        $genresList = $this->feedService->getGenresByParentIds($genres);
        return array_unique(array_merge($genres, $genresList, $upperGenreIds));
    }


    /**
     * @param int $id
     * @param bool $isCourse
     * @return int
     */
    protected function getCourse(int $id, bool $isCourse = true): int
    {
        $idsCourse = $this->feedService->getCoursesByIdProduct($id, isCourse: $isCourse);
        if (count($idsCourse) == 1) {
            $idCourse = $idsCourse[0]['id'];
        } else {
            $parentCourseIds = array_column($idsCourse, 'parent_id', 'id');
            $courseParentIds = array_column($idsCourse, 'id', 'parent_id');
            $idCourse = current(array_intersect($parentCourseIds, $courseParentIds));
            if ($idCourse == 0) {
                ksort($parentCourseIds);
                $idCourse = key($parentCourseIds);
            }
        }
        return $idCourse;
    }

    /**
     * @return string
     */
    protected function getHeader(): string
    {
        $footer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<yml_catalog date=\"" . date("Y-m-d H:i") . "\">\n";
        $footer .= "<shop>\n<name>Deltabook</name>\n<company>ООО \"Цунами Букс\"</company>\n";
        $footer .= "<url>https://deltabook.ru/</url>\n<currencies>\n<currency id=\"RUR\" rate=\"1\" />\n</currencies>\n";

        return $footer;
    }

    /**
     * @return string
     */
    protected function getFooter(): string
    {
        return "</shop>\n</yml_catalog>";
    }

    /**
     * @return string
     */
    protected function createCategories(): string
    {
        $categories = "<categories>\n";
        $this->genreService->setParams(['id' => $this->getGenresIdsList()]);
        $this->genreService->setOrder(['id' => 'ASC']);
        foreach ($this->genreService->getGenres() as $genre) {
            $parentId = $genre['parentId'] ? " parentId=\"{$genre['parentId']}\"" : '';
            $name = htmlspecialchars($genre['name']);
            $categories .= "<category id=\"{$genre['id']}\"$parentId>{$name}</category>\n";
            if ($genre['isCourse']) {
                $this->categoryIds[] = $genre['id'];
            }
        }
        $categories .= "</categories>\n";

        return $categories;
    }


    /**
     * @return string
     * @throws Exception
     */
    protected function createOffers(): string
    {
        $offers = "<offers>\n";
        if ($this->params == 'others') {
            $categoryIds = $this->getGenresIdsList();
        } else {
            $categoryIds = $this->categoryIds;
        }
        foreach ($this->getProductsInfoByGenres($categoryIds) as $product) {
            $sizeParams = (isset($product['size']) && $product['size']) ? explode('x', $product['size']) : [];
            $newPrice = $product['price'] * 0.8;

            $annotation = strip_tags($product['annotation']);
            $annotation = htmlspecialchars($annotation);

            $title = htmlspecialchars($product['title']);

            if ((isset($product['pubName']) && $product['pubName'])) {
                $pubName = htmlspecialchars($product['pubName']);
            } else {
                $pubName = '';
            }

            if (isset($product['authors']) && $product['authors']) {
                $authors = htmlspecialchars($product['authors']);
            } else {
                $authors = '';
            }

            if ($product['intName']) {
                if ($product['intName'] === 18) {
                    $adult = 'true';
                } else {
                    $adult = 'false';
                }
                $age = $product['intName'];
            } else {
                $adult = 'true';
                $age = 18;
            }

            if ($this->params == 'others') {
                $categoryId = $this->getCourse($product['id'], isCourse: false);
            } else {
                $categoryId = $this->getCourse($product['id']);
            }
            $offers .= "<offer id=\"{$product['id']}\" available=\"true\">\n<url>https://deltabook.ru/product/{$product['id']}</url>\n<price>{$newPrice}</price>\n<oldprice>{$product['price']}</oldprice>\n<currencyId>RUR</currencyId>\n";
            $offers .= "<categoryId>{$categoryId}</categoryId>\n";
            $offers .= "<pickup>true</pickup>\n<delivery>true</delivery>\n<name>{$title}</name>\n<age unit=\"year\">$age</age>\n<adult>$adult</adult>\n";
            $offers .= $annotation ? "<description>{$annotation}</description>\n" : '';
            $offers .= $authors ? "<author>{$authors}</author>\n" : '';
            $offers .= "<picture>{$product['cover']}</picture>\n";
            $offers .= $pubName ? "<vendor>{$pubName}</vendor>\n" : '';
            $offers .= (isset($product['weight']) && $product['weight']) ? "<param name=\"Вес\" unit=\"g\">{$product['weight']}</param>\n" : '';
            $offers .= $sizeParams ? "<param name=\"Ширина\" unit=\"mm\">{$sizeParams[0]}</param>\n" : '';
            $offers .= $sizeParams ? "<param name=\"Высота\" unit=\"mm\">{$sizeParams[1]}</param>\n" : '';
            $offers .= $sizeParams ? "<param name=\"Толщина\" unit=\"mm\">{$sizeParams[2]}</param>\n" : '';
            $offers .= (isset($product['color']) && $product['color']) ? "<param name=\"Цвет\">{$product['color']}</param>\n" : '';
            $offers .= "</offer>\n";
        }
        $offers .= "</offers>\n";
        return $offers;
    }

    /**
     * @param $params
     * @return void
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }
}