<?php

namespace common\models\permission;

use yii\db\ActiveRecord;

class PermissionEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{permission}}';
    }

    public function rules(): array
    {
        return [
            [
                [['name', 'entity'], 'string'],
                [['name', 'entity'], 'trim'],
                [['date_create', 'date_update'], 'date', 'format' => 'yyyy-mm-dd H:i:s'],
                ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting']
            ]
        ];
    }
}