<?php

use yii\db\Migration;

/**
 * Class m240212_134303_alter_banners_column_active
 */
class m240212_134303_alter_banners_column_active extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%banners}}', 'is_active', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%banners}}', 'is_active');
    }
}
