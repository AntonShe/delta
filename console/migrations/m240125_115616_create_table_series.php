<?php

use yii\db\Migration;

/**
 * Class m240125_115616_create_table_series
 */
class m240125_115616_create_table_series extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%series}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
            'labirint_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%series}}');
    }
}
