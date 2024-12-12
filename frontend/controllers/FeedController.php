<?php

namespace frontend\controllers;

class FeedController extends AbstractController
{
    public string $genre;
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->genre = $_GET['genre'];
    }
    public function actionGetFile()
    {
        $path = \Yii::getAlias('/var/www/deltabook/current/frontend/web/feeds/');
        $file = $path . $this->genre . ".xml";
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }
    }
}