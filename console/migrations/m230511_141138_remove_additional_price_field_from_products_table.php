<?php

use yii\db\Migration;

/**
 * Class m230511_141138_test
 */
class m230511_141138_remove_additional_price_field_from_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%products}}', 'additional_price');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230511_141138_remove_additional_price_field_from_products_table cannot be reverted.\n";

        return false;
    }
}
