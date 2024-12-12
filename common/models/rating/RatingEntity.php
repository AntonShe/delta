<?php

namespace common\models\rating;

use yii\db\ActiveRecord;

class RatingEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{rating}}';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'product_id', 'value'], 'required'],
            [['user_id', 'product_id'], 'integer'],
            ['value', 'integer', 'min' => 0, 'max' => 5],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}