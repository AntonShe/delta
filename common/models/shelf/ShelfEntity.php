<?php

namespace common\models\shelf;

use yii\db\ActiveRecord;

class ShelfEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{trading_shelves}}';
    }

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        return [
            ['id', 'unique'],
            [['name', 'url_name'], 'string'],
            ['name', 'trim'],
            ['sort', 'integer'],
            ['is_active', 'boolean'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
            [['start_date', 'end_date'], 'default', 'value' => date('Y-m-d'), 'on' => 'create'],
        ];
    }
}