<?php

use yii\db\Migration;

/**
 * Class m230514_142019_create_tbl_delivery_profiles
 */
class m230514_142019_create_tbl_delivery_profiles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{delivery_profiles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'type' => $this->integer(),
            'price' => $this->float(),
            'point_id' => $this->integer(),
            'coordinates' => $this->text(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-delivery_profiles-user_id',
            'delivery_profiles',
            'user_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'fk-delivery_profiles-point_id',
            'delivery_profiles',
            'point_id',
            'lpost_delivery_points',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-delivery_profiles-user_id', 'delivery_profiles');

        $this->dropForeignKey('fk-delivery_profiles-point_id', 'delivery_profiles');

        $this->dropTable('{{delivery_profiles}}');
    }
}
