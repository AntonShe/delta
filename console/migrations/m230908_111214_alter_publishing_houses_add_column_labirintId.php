<?php

use yii\db\Migration;

/**
 * Class m230908_111214_alter_publishing_houses_add_column_labirintId
 */
class m230908_111214_alter_publishing_houses_add_column_labirintId extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%publishing_houses}}', 'labirint_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%publishing_houses}}', 'labirint_id');
    }
}
