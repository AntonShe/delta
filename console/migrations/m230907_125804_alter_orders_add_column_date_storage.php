<?php

use yii\db\Migration;

/**
 * Class m230907_125804_alter_orders_add_column_date_storage
 */
class m230907_125804_alter_orders_add_column_date_storage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'date_storage', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'date_storage');
    }
}
