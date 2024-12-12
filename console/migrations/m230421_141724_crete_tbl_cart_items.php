<?php

use yii\db\Migration;

/**
 * Class m230421_141724_crete_tbl_cart_items
 */
class m230421_141724_crete_tbl_cart_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{cart_items}}', [
            'id' => $this->primaryKey(),
            'cart_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'default_price' => $this->float()->notNull()->defaultValue(0),
            'final_price' => $this->float()->notNull()->defaultValue(0),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-cart_items-cart_id',
            'cart_items',
            'cart_id',
            'cart',
            'id'
        );

        $this->addForeignKey(
            'fk-cart_items-product_id',
            'cart_items',
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
        $this->dropForeignKey('fk-cart_items-cart_id', 'cart_items');

        $this->dropForeignKey('fk-cart_items-product_id', 'cart_items');

        $this->dropTable('{{cart_items}}');
    }
}
