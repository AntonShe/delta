<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_genres}}`.
 */
class m230324_134419_create_product_genres_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_genres}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'genre_id' => $this->integer()->notNull(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_genres}}');
    }
}
