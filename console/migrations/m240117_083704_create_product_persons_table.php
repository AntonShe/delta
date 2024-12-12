<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_persons}}`.
 */
class m240117_083704_create_product_persons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_persons}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'person_id' => $this->integer()->notNull(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex(
            'idx-product_persons-product_id',
            'product_persons',
            'product_id'
        );
        $this->addForeignKey(
            'fk-product_persons-product_id',
            'product_persons',
            'product_id',
            'products',
            'id'
        );

        $this->createIndex(
            'idx-product_persons-person_id',
            'product_persons',
            'person_id'
        );
        $this->addForeignKey(
            'fk-product_persons-person_id',
            'product_persons',
            'person_id',
            'persons',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_persons-product_id', 'product_persons');
        $this->dropIndex('idx-product_persons-product_id', 'product_persons');

        $this->dropForeignKey('fk-product_persons-person_id', 'product_persons');
        $this->dropIndex('idx-product_persons-person_id', 'product_persons');

        $this->dropTable('{{%product_persons}}');
    }
}
