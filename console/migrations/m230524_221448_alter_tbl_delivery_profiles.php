<?php

use yii\db\Migration;

/**
 * Class m230524_221448_alter_tbl_delivery_profiles
 */
class m230524_221448_alter_tbl_delivery_profiles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%delivery_profiles}}', 'address', $this->text());
        $this->addColumn('{{%delivery_profiles}}', 'flat', $this->string());
        $this->addColumn('{{%delivery_profiles}}', 'entry', $this->string());
        $this->addColumn('{{%delivery_profiles}}', 'entry_code', $this->string());
        $this->addColumn('{{%delivery_profiles}}', 'flor', $this->string());
        $this->addColumn('{{%delivery_profiles}}', 'comment', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%delivery_profiles}}', 'address');
        $this->dropColumn('{{%delivery_profiles}}', 'flat');
        $this->dropColumn('{{%delivery_profiles}}', 'entry');
        $this->dropColumn('{{%delivery_profiles}}', 'entry_code');
        $this->dropColumn('{{%delivery_profiles}}', 'flor');
        $this->dropColumn('{{%delivery_profiles}}', 'comment');
    }
}
