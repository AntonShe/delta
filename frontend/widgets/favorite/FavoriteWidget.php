<?php

namespace frontend\widgets\favorite;

use yii\base\Widget;

class FavoriteWidget extends Widget
{
    protected bool $status;

    public ?int $id = 0;
    public ?array $additionalClasses = [];

    public function init()
    {
        parent::init();
        FavoriteAsset::register($this->view);

        $this->status = in_array($this->id, \Yii::$app->user->getFavoriteBooks());
    }

    public function run()
    {
        return $this->render('index', [
            'id' => $this->id,
            'status' => $this->status,
            'classes' => implode(' ', $this->additionalClasses)
        ]);
    }
}