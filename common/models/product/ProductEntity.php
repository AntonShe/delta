<?php

namespace common\models\product;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ProductEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{products}}';
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
            [['id', 'labirint_id'], 'unique'],
            [
                ['title', 'isbn', 'page_material', 'binding_material', 'annotation', 'short_annotation', 'cover', 'size',
                    'color', 'authors'], 'trim'
            ],
            [
                ['title', 'isbn', 'page_material', 'binding_material', 'short_annotation', 'annotation', 'cover', 'size',
                    'color', 'authors'], 'string'
            ],
            [
                ['id', 'labirint_id', 'price', 'quantity', 'publishing_house_id', 'series_id', 'publishing_year',
                'pages_number', 'volumes_count', 'nds'], 'integer'
            ],
            ['weight', 'double'],
            [['active', 'is_new', 'is_popular'], 'boolean'],
            ['pdf', 'file', 'extensions' => 'pdf'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
            ['is_new', 'default', 'value' => 1, 'on' => 'create'],
        ];
    }
}