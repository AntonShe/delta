<?php

namespace common\models;

class AbstractDTO
{
    protected $entity;

    public static function make($model)
    {
        $className = static::class;
        return new $className($model);
    }

    public function __construct(array|object $data)
    {
        if (!is_array($data)) {
            $data = (object)$data->toArray();
        }
        $this->entity = (object)$data;
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
}