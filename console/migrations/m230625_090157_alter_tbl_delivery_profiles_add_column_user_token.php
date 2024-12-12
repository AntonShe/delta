<?php

use yii\db\Migration;

/**
 * Class m230625_090157_alter_tbl_delivery_profiles_add_column_user_token
 */
class m230625_090157_alter_tbl_delivery_profiles_add_column_user_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%delivery_profiles}}', 'user_token', $this->string());

        $this->createIndex(
            'idx-delivery_profiles-user_token',
            'delivery_profiles',
            'user_token'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-delivery_profiles-user_token', 'delivery_profiles');

        $this->dropColumn('{{%delivery_profiles}}', 'user_token');
    }
}
