<?php

use yii\db\Migration;

/**
 * Class m230505_142307_create_tbl_lpost_delivery_points
 */
class m230505_142307_create_tbl_lpost_delivery_points extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{lpost_delivery_points}}', [
            'id' => $this->primaryKey(),
            'id_point' => $this->integer()->unique(),
            'latitude' => $this->float(),
            'longitude' => $this->float(),
            'city_name' => $this->string(),
            'id_region' => $this->integer(),
            'is_courier' => $this->boolean(),
            'is_cash' => $this->boolean(),
            'is_card' => $this->boolean(),
            'address' => $this->string(),
            'location_description' => $this->text(),
            'metro' => $this->string(),
            'fitting_rooms_count' => $this->integer(),
            'is_content_requires' => $this->boolean(),
            'photos' => $this->text(),
            'work_hours' => $this->text(),
            'courier_polygons' => $this->text(),
            'phone' => $this->string(),
            'is_del' => $this->boolean()->defaultValue(false),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-lpost_delivery_points-id_point',
            'lpost_delivery_points',
            'id_point'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-lpost_delivery_points-id_point', 'lpost_delivery_points');

        $this->dropTable('{{lpost_delivery_points}}');
    }
}
