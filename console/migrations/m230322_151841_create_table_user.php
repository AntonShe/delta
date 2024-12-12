<?php

use yii\db\Migration;

/**
 * Class m230322_151841_create_table_user
 */
class m230322_151841_create_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{user}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->integer()->unique(),
            'email' => $this->string(200)->unique(),
            'password' => $this->string(256),
            'first_name' => $this->string(150),
            'second_name' => $this->string(150),
            'last_name' => $this->string(150),
            'session_key' => $this->string(256),
            'is_active' => $this->boolean()->defaultValue(true),
            'user_type' => $this->tinyInteger()->defaultValue(0)->notNull(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-user-phone',
            'user',
            'phone'
        );

        $this->createIndex(
            'idx-user-email',
            'user',
            'email'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-phone', 'user');
        $this->dropIndex('idx-user-email', 'user');

        $this->dropTable('{{user}}');
    }
}