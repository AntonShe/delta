<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/base/index.js',
        'js/base/animations.js',
        'js/base/preloader.js',
        'js/base/share.js',
        'js/cart/index.js',
        'js/base/tooltip.js',
        'js/location/index.js',
        'js/product/big-card.js',
        'https://enterprise.api-maps.yandex.ru/2.1?apikey=4edb1bc3-88e7-4fb0-896a-6493d6fa6c25&lang=ru_RU',
    ];

    public $css = [];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
        'frontend\assets\LodashAsset',
        'frontend\assets\SwiperAsset',
        'frontend\assets\EcommerceAsset',
    ];

    public function __construct($config = [])
    {
        parent::__construct($config);

        $local = \Yii::$app->params['isLocal'] ? 'http://localhost:6066/' : '';

        $this->css[] = "{$local}novue/css/style.min.css?v=2.927";
        $this->js[] = "{$local}novue/js/main.js?v=2.927";

        if (\Yii::$app->user->isGuest) {
            $this->depends[] = 'frontend\assets\LoginFormAsset';
        }
    }
}
