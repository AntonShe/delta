<?php

namespace common\models\description;

use yii\db\ActiveRecord;

class DescriptionEntity extends ActiveRecord
{
    public static function tableName(): string
    {
       return '{{descriptions}}';
    }

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        return [
            [['main_text', 'text', 'link', 'name'], 'string'],
            [['main_text', 'text', 'link', 'name'], 'trim'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            ['is_del', 'boolean']
        ];
    }
}