<?php

use yii\db\Migration;

/**
 * Class m240215_145107_create_table_sets_genres
 */
class m240215_145107_create_table_sets_genres extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sets_genres}}', [
            'id' => $this->primaryKey(),
            'genre_id' => $this->integer()->notNull(),
            'set_id' => $this->integer()->notNull(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-sets_genres-genre_id',
            'sets_genres',
            'genre_id'
        );
        $this->addForeignKey(
            'fk-sets_genres-genre_id',
            'sets_genres',
            'genre_id',
            'genres',
            'id'
        );

        $this->createIndex(
            'idx-sets_genres-set_id',
            'sets_genres',
            'set_id'
        );
        $this->addForeignKey(
            'fk-sets_genres-set_id',
            'sets_genres',
            'set_id',
            'sets',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sets_genres-genre_id', 'sets_genres');
        $this->dropIndex('idx-sets_genres-genre_id', 'sets_genres');

        $this->dropForeignKey('fk-sets_genres-set_id', 'sets_genres');
        $this->dropIndex('idx-sets_genres-set_id', 'sets_genres');

        $this->dropTable('{{%sets_genres}}');
    }
}
