<?php

use yii\db\Migration;

/**
 * Class m230414_150050_test
 */
class m230414_150050_add_is_new_field_to_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%products}}', 'is_new', $this->boolean()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%products}}', 'is_new');
    }
}
