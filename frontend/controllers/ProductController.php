<?php

namespace frontend\controllers;

use common\models\genre\GenreService;
use common\models\product\ProductService;
use common\models\redirect\RedirectRepository;
use yii\db\Exception;
use yii\web\Response;

/**
 * Site controller
 */
class ProductController extends AbstractController
{
    protected bool $isLocal = false;
    protected bool $isGuest = true;
    protected ProductService $service;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->service = new ProductService();
        $this->addParams(['isNew' => 0, 'active' => 1]);
    }

    public function actionIndex(int $id): Response|string
    {
        if (!$id) {
            return $this->redirect('/');
        }

        $redirectId = (new RedirectRepository())->getRedirectId($id);
        if ($redirectId) {
            return $this->redirect("/product/{$redirectId['id_to']}", 301);
        }

        $this->service->setParams(['id' => $id, 'withPagination' => false]);

        $data = $this->service->getBigCard();

        if (!$data) {
            return $this->redirect('/');
        }

        return $this->render('index', [
            'data' => $data,
            'similarProducts' => $this->getSimilarProducts($data->genres, $id),
        ]);
    }

    private function getSimilarProducts(array $genres, int $id): array
    {
        $similarProducts = [];
        $similarGenres = [];
        $genreService = new GenreService();
        $genreService->setParams(['id' => array_column($genres, 'id'), 'isCourse' => 1]);

        foreach ($genreService->getGenres() as $similarGenre) {
            if ($similarGenre['isCourse']) {
                $similarGenres[] = $similarGenre;
            }
        }
//        var_dump($similarGenres);exit();
        if (!empty($similarGenres)) {
            $this->service->setParams([
                'genres' => array_column($similarGenres, 'id'),
                'exclude' => ['id' => $id],
                'limit' => 18,
                'isNew' => 0,
                'active' => 1,
            ]);
            $similarProducts = $this->service->getProductsForCatalog()['products'];
        }

        return $similarProducts;
    }
}
