<?php

namespace common\models\series;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class SeriesEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{series}}';
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

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'unique'],
            ['name', 'string'],
            ['name', 'trim'],
            [['id', 'labirint_id','publishing_house_id'],'integer'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ]);
    }

}