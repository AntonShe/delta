<?php

namespace frontend\models\login;

use yii\base\ViewRenderer;
use yii\web\Controller;

class LoginFormService
{
    const FORM_TYPES = [
        0 => 'form0.php',
        1 => 'form1.php',
    ];

    const SUB_FORM = [
        0 => '',
        1 => 'form01.php',
        2 => 'form02.php',
        3 => 'form03.php',
        4 => 'form04.php',
        5 => 'form05.php',
        6 => 'form06.php',
        7 => 'form07.php',
    ];

    private function __construct()
    {
        return false;
    }

    public static function renderForm(array $params): string
    {
        if (!array_key_exists($params['type'], self::FORM_TYPES)) return '';

        if (!array_key_exists($params['subFirst'], self::SUB_FORM)) return '';

        if (!array_key_exists($params['subSecond'], self::SUB_FORM)) return '';

        if (!is_array($params['params'])) return '';

        $params['params']['otherContent'] = $params['subFirst'] != 0
            ?\Yii::$app->controller->renderPartial(
            'loginForm/' . self::SUB_FORM[$params['subFirst']],
            $params['params']) : '';

        $params['params']['otherContent'] .= $params['subSecond'] != 0
            ?\Yii::$app->controller->renderPartial(
            'loginForm/' . self::SUB_FORM[$params['subSecond']],
            $params['params']) : '';

        return \Yii::$app->controller->renderPartial(
        'loginForm/' . self::FORM_TYPES[$params['type']],
        $params['params']);
    }
}