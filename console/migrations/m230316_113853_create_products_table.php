<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 */
class m230316_113853_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'isbn' => $this->string()->notNull(),
            'price' => $this->integer(),
            'quantity' => $this->integer()->notNull(),
            'publishing_house_id' => $this->integer(),
            'publishing_year' => $this->integer(),
            'page_material' => $this->string(),
            'binding_material' => $this->string(),
            'pages_number' => $this->integer(),
            'size' => $this->string(11),
            'weight' => $this->float(),
            'annotation' => $this->text(),
            'genre_id' => $this->integer(),
            'course_id' => $this->integer(),
            'level_id' => $this->integer(),
            'cover' => $this->string(),
            'pdf' => $this->string(),
            'active' => $this->boolean(),
            'rating' => $this->integer(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
        $this->createIndex(
            'idx-products-publishing_house_id',
            'products',
            'publishing_house_id'
        );
        $this->addForeignKey(
            'fk-products-publishing_house_id',
            'products',
            'publishing_house_id',
            'publishing_houses',
            'id'
        );

        $this->createIndex(
            'idx-products-course_id',
            'products',
            'course_id'
        );
        $this->addForeignKey(
            'fk-products-course_id',
            'products',
            'course_id',
            'courses',
            'id'
        );

        $this->createIndex(
            'idx-products-level_id',
            'products',
            'level_id'
        );
        $this->addForeignKey(
            'fk-products-level_id',
            'products',
            'level_id',
            'levels',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk-products-publishing_house_id', 'products');
        $this->dropIndex('idx-products-publishing_house_id', 'products');

        $this->dropForeignKey('fk-products-course_id', 'products');
        $this->dropIndex('idx-products-course_id', 'products');

        $this->dropForeignKey('fk-products-level_id', 'products');
        $this->dropIndex('idx-products-level_id', 'products');

        $this->dropTable('{{%products}}');
    }
}
