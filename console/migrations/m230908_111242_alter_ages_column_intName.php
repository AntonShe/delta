<?php

use yii\db\Migration;

/**
 * Class m230908_111242_alter_ages_column_intName
 */
class m230908_111242_alter_ages_column_intName extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%ages}}', 'intName', $this->integer()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%ages}}', 'intName');
    }
}
