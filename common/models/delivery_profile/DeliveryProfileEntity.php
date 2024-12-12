<?php

namespace common\models\delivery_profile;

use yii\db\ActiveRecord;

class DeliveryProfileEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{delivery_profiles}}';
    }

    public function rules(): array
    {
        return [
            [['user_id','type','point_id'], 'integer'],
            [['price'], 'number'],
            [[
                'coordinates',
                'address',
                'flat',
                'entry',
                'entry_code',
                'flor',
                'comment',
                'user_token',
            ], 'string'],
            [[
                'coordinates',
                'address',
                'flat',
                'entry',
                'entry_code',
                'flor',
                'comment',
                'user_token',
            ], 'trim', 'skipOnEmpty' => true],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}