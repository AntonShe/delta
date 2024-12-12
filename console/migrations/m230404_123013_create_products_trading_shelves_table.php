<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products_trading_shelves}}`.
 */
class m230404_123013_create_products_trading_shelves_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products_trading_shelves}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'trading_shelf_id' => $this->integer()->notNull(),
            'sort' => $this->integer(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-products_trading_shelves-product_id',
            'products_trading_shelves',
            'product_id'
        );
        $this->addForeignKey(
            'fk-products_trading_shelves-product_id',
            'products_trading_shelves',
            'product_id',
            'products',
            'id'
        );

        $this->createIndex(
            'idx-products_trading_shelves-trading_shelf_id',
            'products_trading_shelves',
            'trading_shelf_id'
        );
        $this->addForeignKey(
            'fk-products_trading_shelves-trading_shelf_id',
            'products_trading_shelves',
            'trading_shelf_id',
            'trading_shelves',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-products_trading_shelves-product_id', 'products_trading_shelves');
        $this->dropIndex('idx-products_trading_shelves-product_id', 'products_trading_shelves');

        $this->dropForeignKey('fk-products_trading_shelves-trading_shelf_id', 'products_trading_shelves');
        $this->dropIndex('idx-products_trading_shelves-trading_shelf_id', 'products_trading_shelves');

        $this->dropTable('{{%products_trading_shelves}}');
    }
}
