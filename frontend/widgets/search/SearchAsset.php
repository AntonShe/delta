<?php

namespace frontend\widgets\search;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SearchAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'vue/js/search.js',
    ];

    public $depends = [
//        'frontend\assets\AxiosAsset'
    ];
}
