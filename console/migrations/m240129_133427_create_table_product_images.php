<?php

use yii\db\Migration;

/**
 * Class m240129_133427_create_table_product_images
 */
class m240129_133427_create_table_product_images extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_images}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'type_img' => $this->integer()->notNull(),
            'url' => $this->string(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-product_images-product_id',
            'product_images',
            'product_id'
        );
        $this->addForeignKey(
            'fk-product_images-product_id',
            'product_images',
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
        $this->dropForeignKey('fk-product_images-product_id', 'product_images');
        $this->dropIndex('idx-product_images-product_id', 'product_images');

        $this->dropTable('{{%product_images}}');
    }
}
