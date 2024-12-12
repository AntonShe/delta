<?php

use yii\db\Migration;

/**
 * Class m230515_103625_create_tbl_favorite
 */
class m230515_103625_create_tbl_favorite extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{favorite}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'session_key' => $this->string(),
            'product_id' => $this->integer(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-favorite-user_id',
            'favorite',
            'user_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'fk-favorite-product_id',
            'favorite',
            'product_id',
            'products',
            'id'
        );

        $this->createIndex(
            'idx-favorite-session_key',
            'favorite',
            'session_key'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-favorite-user_id', 'favorite');

        $this->dropForeignKey('fk-favorite-product_id', 'favorite');

        $this->dropIndex('idx-favorite-session_key', 'favorite');

        $this->dropTable('{{favorite}}');
    }
}
