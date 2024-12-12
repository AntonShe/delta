<?php

use yii\db\Migration;

/**
 * Class m230411_152351_test
 */
class m230411_153101_update_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%products}}', 'color', $this->string(255));
        $this->addColumn('{{%products}}', 'volumes_count', $this->integer());
        $this->addColumn('{{%products}}', 'is_popular', $this->boolean()->defaultValue(0));
        $this->addColumn('{{%products}}', 'id_labirint', $this->integer());
        $this->dropColumn('{{%products}}', 'author_id');
        $this->dropColumn('{{%products}}', 'genre_id');
        $this->dropColumn('{{%products}}', 'rating');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%products}}', 'color');
        $this->dropColumn('{{%products}}', 'volumes_count');
        $this->dropColumn('{{%products}}', 'is_popular');
        $this->dropColumn('{{%products}}', 'id_labirint');
        $this->addColumn('{{%products}}', 'author_id', $this->integer());
        $this->addColumn('{{%products}}', 'genre_id', $this->integer());
        $this->addColumn('{{%products}}', 'rating', $this->integer());
    }
}
