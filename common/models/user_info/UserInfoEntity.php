<?php

namespace common\models\user_info;

use yii\db\ActiveRecord;

class UserInfoEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{user_info}}';
    }

    public function rules(): array
    {
        return [
            ['city', 'string'],
            ['user_id', 'integer'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}