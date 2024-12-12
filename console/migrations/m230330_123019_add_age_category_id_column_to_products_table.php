<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%products}}`.
 */
class m230330_123019_add_age_category_id_column_to_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%products}}', 'age_category_id', $this->integer()->defaultValue(null));

        $this->createIndex(
            'idx-products-age_category_id',
            'products',
            'age_category_id'
        );
        $this->addForeignKey(
            'fk-products-age_category_id',
            'products',
            'age_category_id',
            'product_age_categories',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-products-age_category_id', 'products');
        $this->dropIndex('idx-products-age_category_id', 'products');
        $this->dropColumn('{{%products}}', 'age_category_id');
    }
}
