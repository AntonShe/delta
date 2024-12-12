<?php

use yii\db\Migration;

/**
 * Class m240215_144717_create_table_sets
 */
class m240215_144717_create_table_sets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sets}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sets}}');
    }
}
