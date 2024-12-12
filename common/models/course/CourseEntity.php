<?php

namespace common\models\course;

use yii\db\ActiveRecord;

class CourseEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{courses}}';
    }

    public function rules(): array
    {
        return [
            ['id', 'unique'],
            ['id', 'integer'],
            [['name', 'annotation', 'description', 'link'], 'trim'],
            [['name', 'annotation', 'description', 'link'], 'string'],
            ['logo', 'image'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}