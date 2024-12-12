<?php

namespace common\models\genre;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class GenreEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{genres}}';
    }

    public function formName(): string
    {
        return '';
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
            ['id', 'unique'],
            [['name', 'description', 'cover'], 'trim'],
            [['name', 'description', 'cover'], 'string'],
            [['parent_id', 'id', 'sort', 'level'], 'integer'],
            [['is_course', 'on_main', 'popular'], 'boolean'],
            ['parent_id', 'exist', 'targetClass' => GenreEntity::class, 'targetAttribute' => 'id', 'skipOnEmpty' => true],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}