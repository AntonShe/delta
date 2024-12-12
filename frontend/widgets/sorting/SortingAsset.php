<?php

namespace frontend\widgets\sorting;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SortingAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/sorting/assets');

    public $js = [
        'main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
    ];
}
