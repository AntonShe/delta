<?php

use yii\db\Migration;

/**
 * Class m230512_101154_test
 */
class m230512_101154_re extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{authors}}');
        $this->dropTable('{{courses}}');
        $this->dropTable('{{product_authors}}');

        $this->addColumn('{{%products}}', 'authors', $this->string());
        $this->addColumn('{{%products}}', 'labirint_id', $this->integer());

        $this->addColumn('{{%genres}}', 'description', $this->text());
        $this->addColumn('{{%genres}}', 'cover', $this->integer());
        $this->addColumn('{{%genres}}', 'is_course', $this->boolean());
        $this->addColumn('{{%genres}}', 'on_main', $this->boolean());
        $this->addColumn('{{%genres}}', 'popular', $this->boolean());

        $this->dropColumn('{{%products}}', 'course_id');
        $this->dropColumn('{{%products}}', 'age_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
