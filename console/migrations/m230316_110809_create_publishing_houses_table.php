<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%publishing_houses}}`.
 */
class m230316_110809_create_publishing_houses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%publishing_houses}}', [
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
        $this->dropTable('{{%publishing_houses}}');
    }
}
