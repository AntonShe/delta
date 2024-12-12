<?php

use yii\db\Migration;

/**
 * Class m230416_141702_add_additional_price_field_to_products_table
 */
class m230416_141702_add_additional_price_field_to_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%products}}', 'additional_price', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%products}}', 'additional_price');
    }
}
