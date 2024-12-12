<?php

use yii\db\Migration;

/**
 * Class m240215_092234_alter_trading_shelves_add_column
 */
class m240215_092234_alter_trading_shelves_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trading_shelves}}', 'url_name', $this->string()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%trading_shelves}}', 'url_name');
    }
}
