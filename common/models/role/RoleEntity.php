<?php

namespace common\models\role;

use yii\db\ActiveRecord;

class RoleEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{role}}';
    }

    public function rules(): array
    {
        return [
            [
                ['name', 'string'],
                ['name', 'trim'],
                [['date_create', 'date_update'], 'date', 'format' => 'yyyy-mm-dd H:i:s'],
                ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting']
            ]
        ];
    }
}