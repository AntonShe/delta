<?php

namespace common\models\set;

use yii\db\ActiveRecord;

class SetEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{sets}}';
    }

    public function rules(): array
    {
        return [
            ['id', 'unique'],
            ['name', 'string'],
            ['name', 'trim'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}