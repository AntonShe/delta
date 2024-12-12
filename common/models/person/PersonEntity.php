<?php

namespace common\models\person;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class PersonEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{persons}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'date_created',
                'updatedAtAttribute' => 'date_updated',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['id'], 'unique'],
            [
                ['name_full', 'name_full_ru', 'alternative_name', 'description', 'seo_title', 'seo_meta_keywords',
                    'cover', 'seo_meta_description'], 'trim'
            ],
            [
                ['name_full', 'name_full_ru', 'alternative_name', 'description', 'seo_title', 'seo_meta_keywords',
                    'cover', 'seo_meta_description'], 'string'
            ],
            [
                ['id', 'labirint_id'], 'integer'
            ],
            [['active'], 'boolean'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
            ['active', 'default', 'value' => 1, 'on' => 'create'],
        ];
    }
}