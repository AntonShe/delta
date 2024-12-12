<?php

namespace common\models\rsb_transaction;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class RsbTransactionEntity extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{rsb_transactions}}';
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

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['transaction_id', 'uniq_id', 'client_ip_addr'], 'string'],
            [['order_number','user_id', 'status', 'type'], 'integer'],
            ['amount', 'number'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
        ];
    }
}