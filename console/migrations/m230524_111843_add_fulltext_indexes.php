<?php

use yii\db\Migration;

/**
 * Class m230524_111843_add_fulltext_indexes
 */
class m230524_111843_add_fulltext_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE products ADD FULLTEXT (title, annotation);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            ALTER TABLE products DROP INDEX title;
        ");
    }
}
