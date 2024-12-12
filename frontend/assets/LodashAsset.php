<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LodashAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/lodash';

    public $js = [
        'lodash.min.js',
    ];
}
