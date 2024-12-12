<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AxiosAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/axios/dist';

    public $js = [
        'axios.min.js',
    ];
}
