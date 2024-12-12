<?php

use yii\db\Migration;

/**
 * Class m230915_090141_add_orders_column_possible_delivery_date
 */
class m230915_090141_add_orders_column_possible_delivery_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'possible_delivery_date', $this->date()->after('delivery_date'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'possible_delivery_date');
    }
}
