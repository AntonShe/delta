<?php

namespace common\models\order_item;

use yii\db\ActiveRecord;

class OrderItemEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{order_items}}';
    }

    public function rules(): array
    {
        return [
            [[
                'order_id',
                'product_id',
                'quantity'
            ], 'integer'],
            ['product_price', 'number'],
            [['date_create', 'date_update'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}