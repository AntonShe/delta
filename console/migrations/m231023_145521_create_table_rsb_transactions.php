<?php

use yii\db\Migration;

/**
 * Class m231023_145521_create_table_rsb_transactions
 */
class m231023_145521_create_table_rsb_transactions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{rsb_transactions}}', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->string(100)->notNull()->unique(),
            'order_number' => $this->integer()->notNull(),
            'amount' => $this->float()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'client_ip_addr' => $this->string(20)->notNull(),
            'uniq_id' => $this->string(100)->notNull(),
            'status' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'date_created' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_updated' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{rsb_transactions}}');
    }
}
