<?php

namespace common\models\level;

use yii\db\ActiveRecord;

class LevelEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{levels}}';
    }

    public function rules(): array
    {
        return [
            ['id', 'unique'],
            ['id', 'integer'],
            ['name', 'trim'],
            ['name', 'string'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}