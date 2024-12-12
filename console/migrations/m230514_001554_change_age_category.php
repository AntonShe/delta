<?php

use yii\db\Migration;

/**
 * Class m230514_001554_change_age_category
 */
class m230514_001554_change_age_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('{{%product_age_categories}}', 'product_ages');
        $this->dropColumn('{{%product_ages}}', 'name');
        $this->addColumn('{{%product_ages}}', 'product_id', $this->integer()->notNull());
        $this->addColumn('{{%product_ages}}', 'age_id', $this->integer()->notNull());

        $this->createTable('{{%ages}}', [
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
        $this->renameTable('{{%product_ages}}', 'product_age_categories');
        $this->addColumn('{{%product_age_categories}}', 'name', $this->string()->notNull());
        $this->dropColumn('{{%product_age_categories}}', 'product_id');
        $this->dropColumn('{{%product_age_categories}}', 'age_category_id');
        $this->dropTable('{{%ages}}');
    }
}
