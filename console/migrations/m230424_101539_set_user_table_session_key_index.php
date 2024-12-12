<?php

use yii\db\Migration;

/**
 * Class m230424_101539_set_user_table_session_key_index
 */
class m230424_101539_set_user_table_session_key_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-user-session_key',
            'user',
            'session_key'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-session_key', 'user');
    }
}
