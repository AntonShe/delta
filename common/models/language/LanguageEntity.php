<?php

namespace common\models\language;

use yii\db\ActiveRecord;

class LanguageEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{languages}}';
    }

    public function rules(): array
    {
        return [
            ['id', 'unique'],
            ['id', 'integer'],
            ['name', 'required'],
            ['name', 'string'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}