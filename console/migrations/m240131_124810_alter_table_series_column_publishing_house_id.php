<?php

use yii\db\Migration;

/**
 * Class m240131_124810_alter_table_series_column_publishing_house_id
 */
class m240131_124810_alter_table_series_column_publishing_house_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%series}}', 'publishing_house_id', $this->integer()->after('name'));

        $this->createIndex(
            'idx-series-publishing_house_id',
            'series',
            'publishing_house_id'
        );
        $this->addForeignKey(
            'fk-series-publishing_house_id',
            'series',
            'publishing_house_id',
            'publishing_houses',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-series-publishing_house_id', 'series');
        $this->dropIndex('idx-series-publishing_house_id', 'series');

        $this->dropColumn('{{%series}}', 'publishing_house_id');
    }
}
