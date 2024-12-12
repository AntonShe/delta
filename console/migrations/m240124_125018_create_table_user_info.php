<?php

use yii\db\Migration;

/**
 * Class m240124_125018_create_table_user_info
 */
class m240124_125018_create_table_user_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_info}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'city' => $this->string(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-user_info-user_id',
            'user_info',
            'user_id'
        );
        $this->addForeignKey(
            'fk-user_info-user_id',
            'user_info',
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
        $this->dropForeignKey('fk-user_info-user_id', 'user_info');
        $this->dropIndex('idx-user_info-user_id', 'user_info');

        $this->dropTable('{{%user_info}}');
    }
}
