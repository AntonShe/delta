<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%age_categories}}`.
 */
class m230330_122621_create_product_age_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_age_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_age_categories}}');
    }
}
