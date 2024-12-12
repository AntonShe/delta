<?php

namespace frontend\widgets\header_menu;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class HeaderMenuAsset extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/header_menu/assets');

    public $js = [
        'main.js',
    ];
}
