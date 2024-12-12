<?php

use yii\db\Migration;

/**
 * Class m240125_141331_alter_table_products_add_column_seriesId
 */
class m240125_141331_alter_table_products_add_column_seriesId extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%products}}', 'series_id', $this->integer()->after('publishing_house_id'));

        $this->createIndex(
            'idx-products-series_id',
            'products',
            'series_id'
        );
        $this->addForeignKey(
            'fk-products-series_id',
            'products',
            'series_id',
            'series',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-products-series_id', 'products');
        $this->dropIndex('idx-products-series_id', 'products');

        $this->dropColumn('{{%products}}', 'series_id');
    }
}
