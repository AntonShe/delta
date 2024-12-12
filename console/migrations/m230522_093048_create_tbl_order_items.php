<?php

use yii\db\Migration;

/**
 * Class m230522_093048_create_tbl_order_items
 */
class m230522_093048_create_tbl_order_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{order_items}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'product_id' => $this->integer(),
            'quantity' => $this->integer(),
            'product_price' => $this->float(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-order_items-order_id',
            'order_items',
            'order_id',
            'orders',
            'id'
        );

        $this->addForeignKey(
            'fk-order_items-product_id',
            'order_items',
            'product_id',
            'products',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order_items-order_id', 'order_items');

        $this->dropForeignKey('fk-order_items-product_id', 'order_items');

        $this->dropTable('{{order_items}}');
    }
}
