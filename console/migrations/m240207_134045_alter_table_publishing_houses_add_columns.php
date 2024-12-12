<?php

use yii\db\Migration;

/**
 * Class m240207_134045_alter_table_publishing_houses_add_columns
 */
class m240207_134045_alter_table_publishing_houses_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%publishing_houses}}', 'seo_title', $this->string()->after('description'));
        $this->addColumn('{{%publishing_houses}}', 'seo_meta_keywords', $this->text()->after('seo_title'));
        $this->addColumn('{{%publishing_houses}}', 'seo_meta_description', $this->text()->after('seo_meta_keywords'));
        $this->addColumn('{{%publishing_houses}}', 'is_active', $this->boolean()->after('img')->defaultValue(1));
        $this->renameColumn('{{%publishing_houses}}', 'img','cover');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%publishing_houses}}', 'seo_title');
        $this->dropColumn('{{%publishing_houses}}', 'seo_meta_keywords');
        $this->dropColumn('{{%publishing_houses}}', 'seo_meta_description');
        $this->dropColumn('{{%publishing_houses}}', 'is_active');
        $this->renameColumn('{{%publishing_houses}}', 'cover','img');
    }
}
