<?php

namespace common\models\user;

use yii\db\ActiveRecord;

class UserEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{user}}';
    }

    public function rules(): array
    {
        return [
            [['email', 'phone', 'password', 'first_name', 'second_name', 'last_name', 'session_key'], 'string'],
            [['email', 'password', 'first_name', 'second_name', 'last_name', 'session_key'], 'trim', 'skipOnEmpty' => true],
            [['user_type', 'bitrix_id'], 'number'],
            ['is_active', 'boolean'],
            [['date_create', 'date_update'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting']
        ];
    }
}