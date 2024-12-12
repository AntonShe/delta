<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class VueAppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/base/animations.js',
        'js/base/preloader.js',
        'js/cart/index.js',
        'js/location/index.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\AxiosAsset',
        'frontend\assets\SwiperAsset',
        'frontend\assets\EcommerceAsset',
    ];

    public function __construct($config = [])
    {
        parent::__construct($config);

        $local = \Yii::$app->params['isLocal'] ? 'http://localhost:6066/' : '';

        $this->css[] = "{$local}novue/css/style.min.css?v=2.927";
        $this->js[] = "{$local}vue/js/main.js?v=2.927";

        if (\Yii::$app->user->isGuest) {
            $this->depends[] = 'frontend\assets\LoginFormAsset';
        }
    }
}
