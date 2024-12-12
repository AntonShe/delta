<?php

namespace common\models;

use Exception;
use InvalidArgumentException;
use yii\base\Model;

class Breadcrumbs
{
    const TEMPLATES = [
        'index',
        'product-page',
        'catalog',
        'page',
    ];

    private static ?Breadcrumbs $instance = null;

    public static function getInstance(): Breadcrumbs
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){}

    private function __clone(){}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function setTemplate(): void
    {

    }
}