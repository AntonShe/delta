<?php

use yii\db\Migration;

/**
 * Class m240118_140145_alter_table_publishing_houses_two_columns
 */
class m240118_140145_alter_table_publishing_houses_two_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%publishing_houses}}', 'description', $this->text()->after('name'));
        $this->addColumn('{{%publishing_houses}}', 'img', $this->string()->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%publishing_houses}}', 'description');
        $this->dropColumn('{{%publishing_houses}}', 'img');
    }
}
