<?php

use yii\db\Migration;

/**
 * Class m230626_084854_alter_tbl_order_add_delivery_date
 */
class m230626_084854_alter_tbl_order_add_delivery_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'delivery_date', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'delivery_date');
    }
}
