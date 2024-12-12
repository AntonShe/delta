<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SwiperAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm-asset/swiper';

    public $css = [
        'swiper-bundle.min.css',
    ];

    public $js = [
        'swiper-bundle.min.js',
    ];
}
