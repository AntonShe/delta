<?php

namespace common\models\api\tokens;

use yii\db\ActiveRecord;

class TokenEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{api_tokens_store}}';
    }

    public function rules(): array
    {
        return [
            ['token', 'string'],
            ['token', 'trim', 'skipOnEmpty' => true],
            ['type', 'integer'],
            [['date_create', 'date_update', 'valid_till'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}