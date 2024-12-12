<?php

namespace frontend\widgets\add_to_cart;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AddToCartAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/add_to_cart/assets');

    public $js = [
        'main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
    ];
}
