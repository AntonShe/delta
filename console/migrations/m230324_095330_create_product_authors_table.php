<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_authors}}`.
 */
class m230324_095330_create_product_authors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_authors}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_authors}}');
    }
}
