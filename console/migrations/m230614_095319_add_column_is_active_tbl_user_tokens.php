<?php

use yii\db\Migration;

/**
 * Class m230614_095319_add_column_is_active_tbl_user_tokens
 */
class m230614_095319_add_column_is_active_tbl_user_tokens extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_token}}', 'is_used', $this->smallInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_token}}', 'is_used');
    }
}
