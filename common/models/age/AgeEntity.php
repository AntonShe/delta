<?php

namespace common\models\age;

use yii\db\ActiveRecord;

class AgeEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{ages}}';
    }

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'string'],
            ['intName', 'integer'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}