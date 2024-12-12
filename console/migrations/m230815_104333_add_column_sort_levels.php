<?php

use yii\db\Migration;

/**
 * Class m230815_104333_add_column_sort_levels
 */
class m230815_104333_add_column_sort_levels extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%levels}}', 'sort', $this->integer()->defaultValue(0)->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%levels}}', 'sort');
    }
}
