<?php

namespace common\models\order;

use yii\db\ActiveRecord;

class OrderEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{orders}}';
    }

    public function rules(): array
    {
        return [
            [['session_key', 'manager_comment', 'getter_phone', 'getter_name'], 'string'],
            [['session_key', 'manager_comment', 'getter_phone', 'getter_name'], 'trim', 'skipOnEmpty' => true],
            [[
                'order_number',
                'user_id',
                'delivery_profile_id',
                'payment_type',
                'status',
                'manager_id',
                'status_payment'
            ], 'integer'],
            ['order_price', 'number'],
            [['date_create', 'date_update'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['delivery_date', 'possible_delivery_date', 'date_storage'], 'datetime', 'format' => 'php:Y-m-d'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}