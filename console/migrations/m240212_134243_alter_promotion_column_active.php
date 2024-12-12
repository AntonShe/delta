<?php

use yii\db\Migration;

/**
 * Class m240212_134243_alter_promotion_column_active
 */
class m240212_134243_alter_promotion_column_active extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%promotions}}', 'is_active', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%promotions}}', 'is_active');
    }
}
