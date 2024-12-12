<?php

namespace common\models\userProfile;

use common\models\AbstractRepository;
use yii\db\Exception;

class UserProfileRepository extends AbstractRepository
{
    protected array $fieldsMap = [
        'user_id' => 'userId',
        'is_legal' => 'isLegal',
        'is_payer' => 'isPayer',
        'legal_form' => 'legalForm',
        'legal_name' => 'legalName',
        'legal_address' => 'legalAddress',
        'legal_inn' => 'legalInn',
        'legal_kpp' => 'legalKpp',
        'legal_checking_acc' => 'legalCheckingAcc',
        'legal_bank' => 'legalBank',
        'legal_bik' => 'legalBik',
        'legal_cor_acc' => 'legalCorAcc',
        'legal_bank_book' => 'legalBankBook',
        'legal_signatory_position' => 'legalSignatoryPosition',
        'legal_signatory_name' => 'legalSignatoryName',
        'legal_signatory_base' => 'legalSignatoryBase',
    ];

    public function __construct()
    {
        $this->entity = new UserProfileEntity();
        parent::__construct();
    }

    public function getProfiles(): array
    {
        $isFirst = true;
        $query = $this->entity::find();

        foreach ($this->params as $param => $value) {
            if ($isFirst) {
                $query->where([$param => $value]);
            } else {
                $query->orWhere([$param => $value]);
            }
        }

        $profiles = $query->asArray()
                ->all();

        foreach ($profiles as &$profile) {
            $profile = $this->convertField($profile);
            $profile['birthday'] = empty($profile['birthday'])
                ? $profile['birthday']
                : date('Y-m-d', strtotime($profile['birthday']));
        }
        unset($profile);

        return $profiles;
    }

    public function getProfileBy(array $attributes,  bool $isAnd = false): array
    {
        if (empty($attributes)) return [];

        $query = $this->entity::find();
        $isFirst = true;

        foreach ($attributes as $attribute => $value) {
            if ($value === null) continue;

            if ($isFirst) {
                $isFirst  = false;
                $query->Where([$attribute => $value]);
            } else {
                if ($isAnd) {
                    $query->andWhere([$attribute => $value]);
                } else {
                    $query->orWhere([$attribute => $value]);
                }
            }

        }

        return $query
            ->indexBy('id')
            ->asArray()
            ->all();
    }

    public function createUserProfile(array $data): bool
    {
        foreach ($data as $field => $value)  {
            if ($value === '' || is_null($value)) unset($data[$field]);
        }

        $this->entity->load($data, '');

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                throw new Exception('Не удалось создать профиль.');
            }
        }catch (Exception $e) {
            $transaction->rollBack();
            var_dump($e->getMessage()); die();
            return false;
        }
    }

    public function updateUserProfile(array $data): bool
    {
        $entity = $this->entity::findOne($data['id']);
        $entity->load($data, '');

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            $entity->load($data, '');

            if ($entity->validate() && $entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();

                throw new Exception('Не удалось создать профиль.');
            }
        }catch (\Exception $e) {
            return false;
        }
    }

    public function resetUserProfileEntity()
    {
        $this->entity = new UserProfileEntity();
    }

    public function deleteProfile(int $id): bool
    {
        $entity = $this->entity::findOne($id);

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($entity->delete()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();

                throw new Exception('Не удалось удалить профиль.');
            }
        }catch (\Throwable $e) {
            return false;
        }
    }
}