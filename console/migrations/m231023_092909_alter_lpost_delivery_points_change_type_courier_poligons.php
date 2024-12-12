<?php

use yii\db\Migration;

/**
 * Class m231023_092909_alter_lpost_delivery_points_change_type_courier_poligons
 */
class m231023_092909_alter_lpost_delivery_points_change_type_courier_poligons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%lpost_delivery_points}}', 'courier_polygons', $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%lpost_delivery_points}}', 'courier_polygons', $this->text());
    }

}
