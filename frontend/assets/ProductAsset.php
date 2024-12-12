<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ProductAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/product/index.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
        'frontend\assets\LodashAsset',
    ];
}