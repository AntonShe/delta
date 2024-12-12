<?php

namespace frontend\controllers;

use common\models\genre\GenreService;
use common\models\product\ProductService;
use yii\web\Response;

/**
 * Site controller
 */
class RedirectController extends AbstractController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionProduct(string $code): Response|string
    {
        if (!$code) {
            return $this->redirect('/');
        }

        preg_match('/\-([0-9]+)\z/', $code, $matches);
        $id = $matches[1] ?? null;

        if (!$id) {
            return $this->redirect('/');
        }


        $service = new ProductService();
        $service->setParams([
            'id' => $id,
            'withPagination' => false,
            'isNew' => 0,
            'active' => 1
        ]);

        $data = $service->getProducts();

        if (!empty($data['products'][0])) {
            return $this->redirect("/product/{$id}", 301);
        }

        return $this->redirect('/');
    }
    public function actionCatalog(string $code): Response|string
    {
        if (!$code) {
            return $this->redirect('/');
        }

        preg_match('/^([0-9]+)-/', $code, $matches);
        $id = $matches[1] ?? null;

        if (!$id) {
            return $this->redirect('/');
        }


        $service = new GenreService();
        $service->setParams(['id' => $id]);

        if (!empty($service->getGenres())) {
            return $this->redirect("/catalog/{$id}", 301);
        }

        return $this->redirect('/');
    }
}
