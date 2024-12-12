<?php

namespace common\models\userProfile;

use yii\db\ActiveRecord;

class UserProfileEntity extends ActiveRecord
{
    /*
     * ID
     * ID Пользователя id_user
     * Тип (физ/юр) is_legal bool
     *
     * Физ
     * -----------
     * Дата Рождения (физ) birthday date
     * Пол sex bool
     *
     * Юр. лицо
     * -----------------
     * Тип (плательщик/ получатель) is_payer bool
     * Форма юр.лица legal_form
     * Название организации legal_name
     * Юр. адрес legal_address
     * ИНН legal_inn
     * КПП legal_kpp
     * Расчетный счет legal_checking_acc
     * Банк legal_bank
     * БИК legal_bik
     * Кор. счет legal_cor_acc
     * Лиц. счет legal_bank_book
     * Должность подписанта legal_signatory_position
     * ФИО подписанта legal_signatory_name
     * Действует на основании legal_signatory_base
     *
     * Общие
     * ---------
     * Дата создания date_create
     * Дата изменения date_update
     */

    public static function tableName(): string
    {
        return '{{user_profile}}';
    }

    public function rules():array
    {
        return [
            [
                [
                    'legal_form',
                    'legal_name',
                    'legal_address',
                    'legal_inn',
                    'legal_kpp',
                    'legal_checking_acc',
                    'legal_bank',
                    'legal_bik',
                    'legal_cor_acc',
                    'legal_bank_book',
                    'legal_signatory_position',
                    'legal_signatory_name',
                    'legal_signatory_base'
                ],
                'string'
            ],
            [
                [
                    'legal_form',
                    'legal_name',
                    'legal_address',
                    'legal_inn',
                    'legal_kpp',
                    'legal_checking_acc',
                    'legal_bank',
                    'legal_bik',
                    'legal_cor_acc',
                    'legal_bank_book',
                    'legal_signatory_position',
                    'legal_signatory_name',
                    'legal_signatory_base'
                ],
                'trim',
                'skipOnEmpty' => true
            ],
            [['is_legal', 'is_payer', 'sex'], 'boolean'],
            ['user_id', 'required'],
            ['user_id', 'integer'],
            [['date_create', 'date_update', 'birthday'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
        ];
    }
}
