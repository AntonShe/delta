<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%levels}}`.
 */
class m230316_110641_create_levels_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%levels}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%levels}}');
    }
}
