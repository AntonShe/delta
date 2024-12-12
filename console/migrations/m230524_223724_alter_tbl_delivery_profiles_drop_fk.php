<?php

use yii\db\Migration;

/**
 * Class m230524_223724_alter_tbl_delivery_profiles_drop_fk
 */
class m230524_223724_alter_tbl_delivery_profiles_drop_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-delivery_profiles-point_id', 'delivery_profiles');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey(
            'fk-delivery_profiles-point_id',
            'delivery_profiles',
            'point_id',
            'lpost_delivery_points',
            'id'
        );
    }
}
