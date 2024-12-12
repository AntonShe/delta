<?php

use yii\db\Migration;

/**
 * Class m230411_162908_change_type_user_tbl_phone_col
 */
class m230411_162908_change_type_user_tbl_phone_col extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user', 'phone', 'varchar(18)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('table_name', 'column_name', 'int');
    }
}
