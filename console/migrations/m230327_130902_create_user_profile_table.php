<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile}}`.
 */
class m230327_130902_create_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{user_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'is_legal' => $this->boolean()->notNull(),
            'birthday' => $this->dateTime(),
            'sex' => $this->boolean(),
            'is_payer' => $this->boolean(),
            'legal_form' => $this->string(),
            'legal_name' => $this->string(),
            'legal_address' => $this->string(),
            'legal_inn' => $this->string(),
            'legal_kpp' => $this->string(),
            'legal_checking_acc' => $this->string(),
            'legal_bank' => $this->string(),
            'legal_bik' => $this->string(),
            'legal_cor_acc' => $this->string(),
            'legal_bank_book' => $this->string(),
            'legal_signatory_position' => $this->string(),
            'legal_signatory_name' => $this->string(),
            'legal_signatory_base' => $this->string(),
            'date_create' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'date_update' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-user_profile-legal_inn',
            'user_profile',
            'legal_inn'
        );

        $this->createIndex(
            'idx-user_profile-legal_name',
            'user_profile',
            'legal_name'
        );

        $this->createIndex(
            'idx-user_profile-user_id',
            'user_profile',
            'user_id'
        );

        $this->addForeignKey(
            'fk-user_profile-user_id',
            'user_profile',
            'user_id',
            'user',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user_profile-legal_inn', 'user_profile');

        $this->dropIndex('idx-user_profile-legal_name', 'user_profile');

        $this->dropForeignKey('fk-user_profile-user_id', 'user_profile');
        $this->dropIndex('idx-user_profile-user_id', 'user_profile');

        $this->dropTable('{{user_profile}}');
    }
}
