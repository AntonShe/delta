<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%persons}}`.
 */
class m240117_083557_create_persons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%persons}}', [
            'id' => $this->primaryKey(),
            'name_full' => $this->string(100),
            'name_full_ru' => $this->string(100),
            'alternative_name' => $this->string(100),
            'description' => $this->text(),
            'seo_title' => $this->string(),
            'seo_meta_keywords' => $this->text(),
            'seo_meta_description' => $this->text(),
            'cover' => $this->string(),
            'active' => $this->boolean(),
            'date_created' => $this->dateTime()->notNull(),
            'date_updated' => $this->dateTime()->notNull(),
            'labirint_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%persons}}');
    }
}
