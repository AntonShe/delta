<?php

use yii\db\Migration;

/**
 * Class m230421_141716_crete_tbl_cart
 */
class m230421_141716_crete_tbl_cart extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{cart}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'session_key' => $this->string(),
            'raw_price' => $this->float(),
            'final_price' => $this->float(),
            'discount_sum' => $this->float(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-cart-session_key',
            'cart',
            'session_key'
        );

        $this->addForeignKey(
            'fk-cart-user_id',
            'cart',
            'user_id',
            'user',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-cart-user_id', 'cart');

        $this->dropIndex('idx-cart-session_key', 'cart');

        $this->dropTable('{{cart}}');
    }
}