<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%banners}}`.
 */
class m230404_120856_create_banners_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%banners}}', [
            'id' => $this->primaryKey(),
            'image' => $this->string(),
            'tablet_image' => $this->string(),
            'mobile_image' => $this->string(),
            'title' => $this->string(),
            'text' => $this->text(),
            'link' => $this->string(),
            'sort' => $this->integer(),
            'start_date' => $this->dateTime(),
            'end_date' => $this->dateTime(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
            'is_del' => $this->boolean()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%banners}}');
    }
}
