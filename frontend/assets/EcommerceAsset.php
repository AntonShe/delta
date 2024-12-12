<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EcommerceAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/ecommerce/main.js',
    ];

    public $css = [];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
        'frontend\assets\LodashAsset',
    ];
}
