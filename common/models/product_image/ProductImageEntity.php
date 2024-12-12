<?php

namespace common\models\product_image;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ProductImageEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{product_images}}';
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
                ['url'], 'trim'
            ],
            [
                ['url'], 'string'
            ],
            [
                ['product_id', 'type_img'], 'unique', 'targetAttribute' => ['product_id', 'type_img']
            ],
            [
                ['type_img', 'product_id'], 'integer'
            ],
            [
                ['product_id', 'type_img'], 'required'
            ],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}