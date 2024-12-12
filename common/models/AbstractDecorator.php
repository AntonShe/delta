<?php

namespace common\models;

use yii\base\Model;

class AbstractDecorator
{
    protected $entity;

    public static function decorate($model)
    {
        $className = static::class;
        return new $className($model);
    }

    public function __construct($model)
    {
        $this->entity = $model;
    }

    public function __get($name)
    {
        $methodName = 'get' . $name;
        if (method_exists(static::class, $methodName)) {
            return $this->$methodName();
        } else {
            return $this->entity->{$name};
        }
    }

    public function __call($name, $arguments)
    {
        return $this->entity->$name($arguments);
    }
}