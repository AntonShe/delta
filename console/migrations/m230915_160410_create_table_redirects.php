<?php

use yii\db\Migration;

/**
 * Class m230915_160410_create_table_redirects
 */
class m230915_160410_create_table_redirects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{redirects}}', [
            'id' => $this->primaryKey(),
            'id_from' => $this->integer(),
            'id_to' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{redirects}}');
    }
}
