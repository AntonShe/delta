<?php

use yii\db\Migration;

/**
 * Class m230429_160912_create_tbl_api_tokens_store
 */
class m230429_160912_create_tbl_api_tokens_store extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{api_tokens_store}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull(),
            'token' => $this->string()->notNull(),
            'valid_till' => $this->dateTime()->notNull(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-api_tokens_store-token',
            'api_tokens_store',
            'token'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-api_tokens_store-token', 'api_tokens_store');

        $this->dropTable('{{api_tokens_store}}');
    }
}
