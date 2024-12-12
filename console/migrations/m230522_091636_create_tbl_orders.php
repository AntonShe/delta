<?php

use yii\db\Migration;

/**
 * Class m230522_091636_create_tbl_orders
 */
class m230522_091636_create_tbl_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{orders}}', [
            'id' => $this->primaryKey(),
            'order_number' => $this->bigInteger()->notNull(),
            'user_id' => $this->integer(),
            'session_key' => $this->string(),
            'delivery_profile_id' => $this->integer(),
            'payment_type' => $this->smallInteger(),
            'order_price' => $this->float(),
            'status' => $this->smallInteger(),
            'status_payment' => $this->boolean(),
            'manager_comment' => $this->string(),
            'manager_id' => $this->integer(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-orders-user_id',
            'orders',
            'user_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'fk-orders-delivery_profile_id',
            'orders',
            'delivery_profile_id',
            'delivery_profiles',
            'id'
        );

        $this->createIndex(
            'idx-orders-order_number',
            'orders',
            'order_number'
        );

        $this->createIndex(
            'idx-orders-session_key',
            'orders',
            'session_key'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-orders-user_id', 'orders');

        $this->dropForeignKey('fk-orders-delivery_profile_id', 'orders');

        $this->dropIndex('idx-orders-order_number', 'orders');

        $this->dropIndex('idx-orders-session_key', 'orders');

        $this->dropTable('{{orders}}');
    }
}
