<?php

use yii\db\Migration;

/**
 * Class m240212_134223_alter_trading_shelves_column_active
 */
class m240212_134223_alter_trading_shelves_column_active extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trading_shelves}}', 'is_active', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trading_shelves}}', 'is_active');
    }
}
