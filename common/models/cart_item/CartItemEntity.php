<?php

namespace common\models\cart_item;

use yii\db\ActiveRecord;

class CartItemEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{cart_items}}';
    }

    public function rules(): array
    {
        return [
            [['cart_id', 'product_id', 'quantity'], 'integer'],
            [['default_price', 'final_price'], 'number'],
            [['date_create', 'date_update'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_create', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']]
        ];
    }
}