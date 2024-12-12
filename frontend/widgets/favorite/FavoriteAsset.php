<?php

namespace frontend\widgets\favorite;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FavoriteAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/favorite/assets');

    public $js = [
        'main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
    ];
}
