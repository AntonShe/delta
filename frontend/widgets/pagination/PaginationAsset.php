<?php

namespace frontend\widgets\pagination;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class PaginationAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/pagination/assets');

    public $js = [
        'main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
