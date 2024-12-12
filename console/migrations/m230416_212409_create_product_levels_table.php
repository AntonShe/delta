<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_levels}}`.
 */
class m230416_212409_create_product_levels_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_levels}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'level_id' => $this->integer()->notNull(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->dropForeignKey('fk-products-level_id', 'products');
        $this->dropIndex('idx-products-level_id', 'products');
        $this->dropColumn('products', 'level_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_levels}}');
        $this->addColumn('{{%products%}}', 'level_id', $this->integer());
        $this->createIndex(
            'idx-products-level_id',
            'products',
            'level_id'
        );
        $this->addForeignKey(
            'fk-products-level_id',
            'products',
            'level_id',
            'levels',
            'id'
        );
    }
}
