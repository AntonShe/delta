<?php

use yii\db\Migration;

/**
 * Class m230522_123727_alter_tbl_orders
 */
class m230522_123727_alter_tbl_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'getter_phone', $this->string());
        $this->addColumn('{{%orders}}', 'getter_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'getter_phone');
        $this->dropColumn('{{%orders}}', 'getter_name');
    }

}
