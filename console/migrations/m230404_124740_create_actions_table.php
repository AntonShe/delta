<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%actions}}`.
 */
class m230404_124740_create_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%promotions}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'annotation' => $this->text(),
            'link' => $this->string(),
            'image' => $this->string(),
            'tablet_image' => $this->string(),
            'mobile_image' => $this->string(),
            'start_date' => $this->dateTime(),
            'end_date' => $this->dateTime(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%promotions}}');
    }
}
