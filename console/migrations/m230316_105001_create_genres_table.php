<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%genres}}`.
 */
class m230316_105001_create_genres_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%genres}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'parent_id' => $this->integer(),
            'sort' => $this->integer(),
            'level' => $this->integer(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-genres-parent_id',
            'genres',
            'parent_id'
        );
        $this->addForeignKey(
            'fk-genres-parent_id',
            'genres',
            'parent_id',
            'genres',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk-genres-parent_id', 'genres');
        $this->dropIndex('idx-genres-parent_id', 'genres');

        $this->dropTable('{{%genres}}');
    }
}
