<?php

use yii\db\Migration;

/**
 * Class m230418_135513_create_tbl_user_tokens
 */
class m230418_135513_create_tbl_user_tokens extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{user_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'email' => $this->string(),
            'phone' => $this->string(),
            'pin' => $this->integer()->notNull(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-user_token-email',
            'user_token',
            'email'
        );

        $this->createIndex(
            'idx-user_token-phone',
            'user_token',
            'phone'
        );

        $this->addForeignKey(
            'fk-user_token-user_id',
            'user_token',
            'user_id',
            'user',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user_token-email', 'user_token');

        $this->dropIndex('idx-user_token-phone', 'user_token');

        $this->dropForeignKey('fk-user_token-user_id', 'user_token');

        $this->dropTable('{{user_token}}');
    }
}
