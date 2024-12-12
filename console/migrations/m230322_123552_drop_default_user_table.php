<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{user}}`.
 */
class m230322_123552_drop_default_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{user}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230322_123552_drop_default_user_table cannot be reverted.\n";

        return false;
    }
}