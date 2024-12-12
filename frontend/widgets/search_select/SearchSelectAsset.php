<?php

namespace frontend\widgets\search_select;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SearchSelectAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/search_select/assets');

    public $js = [
        'main.js',
    ];

    public $css = [
        'main.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
    ];
}
