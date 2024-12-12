<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_shelfs}}`.
 */
class m230404_122109_create_trading_shelves_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trading_shelves}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'start_date' => $this->dateTime(),
            'end_date' => $this->dateTime(),
            'sort' => $this->integer(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%trading_shelves}}');
    }
}
