<?php

namespace common\models\user;

use common\models\AbstractDataValidator;
use common\models\publishing_house\PublishingHouseEntity;
use yii\db\PdoValue;

class UserDataValidator extends AbstractDataValidator
{
    const ERRORS = [
        'email' => 'Некорректный E-mail',
        'phone' => 'Некорректный номер телефона',
        'legalInn' => 'Некорректный ИНН',
        'legalKpp' => 'Некорректный КПП',
    ];

    //main user fields
    public ?int $id = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $password = null;
    public ?string $firstName = null;
    public ?string $secondName = null;
    public ?string $lastName = null;
    public ?string $sessionKey = null;
    public ?int $isEmployee = null;
    public ?int $isLegal = null;
    public ?int $payerSameGetter = null;
    public ?string $birthday = null;
    public ?int $sex = null;
    public ?int $pin = null;
    public ?int $isNew = null;
    public ?int $isUpdate = null;
    public ?string $key = null;

    //profile field
    public ?array $profile = null;

    public ?string $role = null;

    //userInfo field
    public ?string $city = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [[
                'email',
                'phone',
                'password',
                'firstName',
                'secondName',
                'lastName',
                'sessionKey',
                'birthday',
                'role',
                'key',
                'city'
            ], 'string'],
            [
                ['email', 'phone', 'password', 'firstName', 'secondName', 'lastName', 'role'],
                'filter',
                'filter' => function($value) {
                    return $value == '' ? null : $value;
                }
            ],
            [['birthday'], 'datetime', 'format' => 'php:Y-m-d'],
            [['id', 'isEmployee', 'isLegal', 'payerSameGetter', 'sex', 'pin', 'isNew', 'isUpdate'], 'integer'],
            [['isEmployee', 'isLegal'], 'default', 'value' => 0],
            ['payerSameGetter', 'default', 'value' => 1],
            ['email', 'emailValidation'],
            ['phone', 'phoneValidation'],
            ['profile', 'profileValidator']
        ]);
    }

    public function emailValidation($attribute)
    {
        $value = $this->$attribute;
        $matches = [];
        $pattern = '/[a-zA-Z]+[a-zA-Z\d\!\#\$\%\&\’\;\+\-\.\=\?\^\_\`\{\}\½\~]*[a-zA-Z\d]*@[a-zA-Z0-9\._-]+\.[a-zA-Z0-9_-]+/';
        preg_match($pattern, $value, $matches);

        if ($matches[0] !== $value && strlen($value) < 50) {
            $this->addError($attribute, self::ERRORS[$attribute]);
        }
    }

    public function phoneValidation($attribute)
    {
        $value = $this->$attribute;
        $matches = [];
        $pattern = '/\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/';
        preg_match($pattern, $value, $matches);

        if ($matches[0] !== $value) {
            $this->addError($attribute, self::ERRORS[$attribute]);
        }
    }

    public function profileValidator($attribute)
    {
        $profiles = $this->$attribute;
        $error = '';

        foreach ($profiles as $profile) {
            $profile['legalForm'] = $profile['legalForm'] == '' ? null : $profile['legalForm'];
            $profile['legalName'] = $profile['legalName'] == '' ? null : $profile['legalName'];
            $profile['legalAddress'] = $profile['legalAddress'] == '' ? null : $profile['legalAddress'];
            $profile['legalCheckingAcc'] = $profile['legalCheckingAcc'] == '' ? null : $profile['legalCheckingAcc'];
            $profile['legalBank'] = $profile['legalBank'] == '' ? null : $profile['legalBank'];
            $profile['legalBik'] = $profile['legalBik'] == '' ? null : $profile['legalBik'];
            $profile['legalCorAcc'] = $profile['legalCorAcc'] == '' ? null : $profile['legalCorAcc'];
            $profile['legalBankBook'] = $profile['legalBankBook'] == '' ? null : $profile['legalBankBook'];
            $profile['legalSignatoryPosition'] = $profile['legalSignatoryPosition'] == '' ? null : $profile['legalSignatoryPosition'];
            $profile['legalSignatoryName'] = $profile['legalSignatoryName'] == '' ? null : $profile['legalSignatoryName'];
            $profile['legalSignatoryBase'] = $profile['legalSignatoryBase'] == '' ? null : $profile['legalSignatoryBase'];

            $profile['legalInn'] == '' ? null : $this->specialInnValidation($error, $profile['legalInn']);
            $profile['legalKpp'] == '' ? null : $this->specialKppValidation($error, $profile['legalKpp']);
        }

        if ($error !== '') {
            $this->addError($attribute, $error);
        }
    }

    private function specialInnValidation(&$error, $attribute)
    {
        switch (strlen($attribute)) {
            case 10:
                $numsSum = (2  * $attribute[0])
                    + (4  * $attribute[1])
                    + (10  * $attribute[2])
                    + (3  * $attribute[3])
                    + (5  * $attribute[4])
                    + (9  * $attribute[5])
                    + (4  * $attribute[6])
                    + (6  * $attribute[7])
                    + (8  * $attribute[8]);

                $sumDiv = (string)($numsSum  % 11);

                if ($attribute[9] != $sumDiv[strlen($sumDiv) - 1]) {
                    $error .= 'Не верно заполнено поле "ИНН": ' . $attribute . "\n";
                }
                break;
            case 12:
                $firstNumsSum = (7  * $attribute[0])
                    + (2  * $attribute[1])
                    + (4  * $attribute[2])
                    + (10  * $attribute[3])
                    + (3  * $attribute[4])
                    + (5  * $attribute[5])
                    + (9  * $attribute[6])
                    + (4  * $attribute[7])
                    + (6  * $attribute[8])
                    + (8  * $attribute[9]);

                $secondNumsSum = (3  * $attribute[0])
                    + (7  * $attribute[1])
                    + (2  * $attribute[2])
                    + (4  * $attribute[3])
                    + (10  * $attribute[4])
                    + (3  * $attribute[5])
                    + (5  * $attribute[6])
                    + (9  * $attribute[7])
                    + (4  * $attribute[8])
                    + (6  * $attribute[9])
                    + (8  * $attribute[10]);

                $firstSumDiv = (string)($firstNumsSum % 11);
                $secondSumDiv = (string)($secondNumsSum % 11);

                if (
                    $attribute[10] != $firstSumDiv[strlen($firstSumDiv) - 1]
                    && $attribute[11] != $secondSumDiv[strlen($secondSumDiv) - 1]
                ) {
                    $error .= 'Не верно заполнено поле "ИНН": ' . $attribute . "\n";
                }
                break;
            default:
                $error .= 'Не верно заполнено поле "ИНН": ' . $attribute . "\n";
                break;
        }
    }

    private function specialKppValidation(&$error, $attribute)
    {
        $isError = false;

        for ($i = 0; $i < strlen($attribute); $i++) {
            $isError = $attribute[$i] !== '0' XOR  intval($attribute[$i]) === 0;
        }

        if ($isError && strlen($attribute) != 9) {
            $error .= 'Не верно заполнено поле "KPP": ' . $attribute . "\n";
        }
    }

    protected function specialDateValidator(&$errors, ?string $date): void
    {
        if (!is_null($date)) {
            $timestamp = strtotime($date);

            if ($timestamp === false || $timestamp >= time()) {
                $errors .= 'Не верно заполнено поле "День рождения": ' . $date . "\n";
            }
        }
    }
}