<?php

namespace common\models\pin;

use yii\db\ActiveRecord;

class PinEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{user_token}}';
    }

    public function rules(): array
    {
        return [
            [['email', 'phone'], 'string'],
            [['email', 'phone'], 'trim', 'skipOnEmpty' => true],
            [['pin', 'user_id', 'is_used'], 'integer'],
            ['date_create', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting']
        ];
    }
}