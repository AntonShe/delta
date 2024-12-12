<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class VueAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/vue/dist';

    public $js = [
        'vue.global.prod.js',
    ];
}
