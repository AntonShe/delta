<?php

namespace common\models\favorite;

use yii\db\ActiveRecord;

class FavoriteEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{favorite}}';
    }

    public function rules(): array
    {
        return [
            [['user_id','product_id'], 'integer'],
            ['session_key', 'string'],
            ['session_key', 'trim', 'skipOnEmpty' => true],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}