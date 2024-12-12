<?php

namespace common\models\publishing_house;

use yii\db\ActiveRecord;

class PublishingHouseEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{publishing_houses}}';
    }

    public function rules(): array
    {
        return [
            ['id', 'unique'],
            ['is_active', 'boolean'],
            [['id', 'labirint_id'], 'integer'],
            [['name','description', 'seo_title', 'seo_meta_keywords', 'seo_meta_description', 'cover'], 'trim'],
            [['name','description', 'seo_title', 'seo_meta_keywords', 'seo_meta_description', 'cover'], 'string'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}