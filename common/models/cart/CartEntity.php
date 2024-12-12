<?php

namespace common\models\cart;

use yii\db\ActiveRecord;

class CartEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{cart}}';
    }

    public function rules(): array
    {
        return [
            ['session_key', 'string'],
            ['session_key', 'trim', 'skipOnEmpty' => true],
            ['user_id', 'integer'],
            [['raw_price', 'final_price', 'discount_sum'], 'number'],
            [['date_create', 'date_update'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}