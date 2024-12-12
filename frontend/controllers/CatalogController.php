<?php

namespace frontend\controllers;

use common\models\genre\GenreDTO;
use common\models\genre\GenreService;
use common\models\product\ProductService;
use common\models\sorting\SortingService;
use yii\web\Response;

class CatalogController extends AbstractController
{
    public string $catalogType = '';
    public ?GenreDTO $genre;
    protected ProductService $service;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->service = new ProductService();
    }

    /**
     * Получение страницы каталога
     */
    public function actionIndex(int $id = null): Response|string
    {
        if (!$id) {
            return $this->redirect('/');
        }

        $this->setGenre($id);

        if (!$this->genre) {
            return $this->redirect('/');
        }

        if ($this->genre->level === 1) {
            $this->catalogType = 'genre';
            return $this->render("product-page", $this->getDataForProduct($id));
        }

        $this->catalogType = 'courses';

        if ($this->genre->isCourse) {
            $this->catalogType = 'course';
            return $this->render("genre-page", $this->getDataForGenre($id));
        }

        $genreChildren = $this->getGenreChildren($id);

        if (!empty($genreChildren) && $genreChildren[0]->isCourse) {
            return $this->render("genres-page", $this->getDataForGenres($genreChildren));
        } elseif ((!empty($genreChildren) && !$genreChildren[0]->isCourse) &&
            isset($this->params['words']) || isset($this->params['parentId'])
        ) {
            return $this->render("genres-page", $this->getDataForGenres([]));
        } elseif (isset($this->params['words']) || isset($this->params['parentId'])) {
            return $this->render("genres-page", $this->getDataForGenres([]));
        }

        $this->catalogType = 'genre';
        return $this->render("product-page", $this->getDataForProduct($id));
    }

    /**
     * Получение списка товаров для страницы каталога
     */
    public function actionAjax(int $id = null): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if (!$id || $this->request->isAjax) {
            return ['errors' => 'Что-то пошло не так'];
        }

        $this->setGenre($id);
        $genreChildren = $this->getGenreChildren($id);

        if (!empty($genreChildren) && $genreChildren[0]->isCourse) {
            return ['html' => $this->getHtml('@app/views/catalog/_genre', $genreChildren)];
        } elseif ((!empty($genreChildren) && !$genreChildren[0]->isCourse) &&
            (isset($this->params['words']) || isset($this->params['parentId']))
        ) {
            return ['html' => $this->getHtml('@app/views/catalog/_genre', [])];
        } elseif (isset($this->params['words']) || isset($this->params['parentId'])) {
            return ['html' => $this->getHtml('@app/views/catalog/_genre', [])];
        }

        if (!isset($this->params['genres'])) {
            $this->addParams(['genres' => [$id], 'active' => 1, 'is_new' => 0]);
        }
        $this->service->setParams($this->params);
        if (isset($this->params['sort']) && $this->params['sort']) {
            $this->service->setOrder([$this->params['sort'] => $this->params['order'] ?? 'asc']);
        }
        $data = $this->service->getProductsForCatalog();

        return [
            'html' => $this->getHtml('@app/views/product_cards/default', $data['products']),
            'pagination' => $data['pagination'],
        ];
    }

    private function getDataForGenre(int $id): array
    {
        $this->service->setParams(['genres' => [$id], 'active' => 1, 'isNew' => 0, 'withPagination' => false]);
        $data = $this->service->getProductsForCatalog();

        $service = new GenreService();
        $service->setParams(['id' => [$this->genre->parentId]]);

        return [
            'genreInfo' => $this->genre,
            'parentGenre' => $service->getGenres()[0],
            'list' => $data['products'],
            'levels' => $this->getProductLevels($data['products']),
            'params' => $this->params,
        ];
    }

    private function getDataForGenres($genreChildren): array
    {
        $service = new GenreService();
        $service->setParams(['id' => [$this->genre->parentId]]);

        return [
            'genreInfo' => $this->genre,
            'parentGenre' => $service->getGenres()[0],
            'list' => $genreChildren,
            'popularList' => $this->getPopularGenres($genreChildren),
            'params' => $this->params,
        ];
    }

    private function getDataForProduct(int $id): array
    {
        if (!isset($this->params['genres'])) {
            $this->addParams(['genres' => [$id], 'active' => 1, 'is_new' => 0]);
        }
        if (isset($this->params['sort']) && $this->params['sort']) {
            $this->service->setOrder([$this->params['sort'] => $this->params['order'] ?? 'asc']);
        }
        $this->service->setParams($this->params);
        $data = $this->service->getProductsForCatalog();

        $this->addParams(['isPopular' => 1, 'withPagination' => false]);
        $this->service->setParams($this->params);
        $popularProducts = $this->service->getProductsForCatalog()['products'];

        return [
            'genreInfo' => $this->genre,
            'pagination' => $data['pagination'],
            'list' => $data['products'],
            'popularList' => $popularProducts,
            'params' => $this->params,
        ];
    }

    private function getProductLevels(array $list): array
    {
        $output = [];

        foreach ($list as $item) {
            foreach ($item->levels as $level) {
                $output[$level['id']] = $level;
            }
        }

        usort($output, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });

        return $output;
    }

    private function getGenreChildren(int $id): array
    {
        $service = new GenreService();
        if (isset($this->params['words']) || isset($this->params['parentId'])) {
            $service->setParams($this->params);
        }
        $service->setOrder(['name' => 'ASC']);

        return SortingService::sortObjectByAttr($service->getGenreChildrenByCatalog($id, 3), 'name');
    }

    private function setGenre(int $id): void
    {
        $service = new GenreService();
        $service->setParams(['id' => $id]);
        $this->genre = $service->getGenresByCatalog()[0] ?? null;
    }

    private function getPopularGenres(array $list): array
    {
        $output = [];
        foreach ($list as $item) {
            if ($item->popular) {
                $output[] = $item;
            }
        }

        return $output;
    }
}
