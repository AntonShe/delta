<?php

namespace frontend\widgets\filters;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FiltersAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/filters/assets');

    public $js = [
        'main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
    ];
}
