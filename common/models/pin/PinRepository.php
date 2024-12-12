<?php

namespace common\models\pin;

use common\models\AbstractRepository;
use common\models\Pagination;
use yii\base\NotSupportedException;
use yii\db\Exception;

class PinRepository extends AbstractRepository
{
    protected $id;
    protected $userId;
    protected $phone;
    protected $email;
    protected $pin;
    protected $isUsed;
    protected $dateCreate;

    protected array $fieldsMap = [
        'user_id' => 'userId',
        'is_used' => 'isUsed',
        'date_create' => 'dateCreate'
    ];

    protected array $params = [];

    public function __construct()
    {
        $this->entity = new PinEntity();
        parent::__construct();
    }

    private function setAttributes(array $attributes): void
    {
        $this->id = $attributes['id'];
        $this->userId = $attributes['userId'];
        $this->phone = $attributes['phone'];
        $this->email = $attributes['email'];
        $this->pin = $attributes['pin'];
        $this->isUsed = $attributes['isUsed'];
        $this->dateCreate = $attributes['dateCreate'];
    }

    public function getPin(): array
    {
        $query = $this->entity::find();

        if (!empty($this->params['userId'])) {
            $query->where(['user_id' => $this->params['userId']]);
        } else if (!empty($this->params['phone'])) {
            $query->where(['phone' => $this->params['phone']]);
        } else if (!empty($this->params['email'])) {
            $query->where(['email' => $this->params['email']]);
        }

        $query->andWhere(['pin' => $this->params['pin']])
            ->andWhere("date_create > DATE_ADD(NOW(), INTERVAL -1 HOUR)")
            ->andWhere("date_create < DATE_ADD(NOW(), INTERVAL 1 HOUR)")
            ->andWhere(['is_used' => 0]);

        $pin = $query->asArray()
            ->one();

        return $pin ?? [];
    }

    public function getLastPin(): array
    {
        $query = $this->entity::find();

        if (!empty($this->params['userId'])) {
            $query->where(['user_id' => $this->params['userId']]);
        } else if (!empty($this->params['phone'])) {
            $query->where(['phone' => $this->params['phone']]);
        } else if (!empty($this->params['email'])) {
            $query->where(['email' => $this->params['email']]);
        }

        $pin = $query->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->one();

        return $pin ?? [];
    }

    public function createPin(): array
    {
        $this->entity->load($this->convertField($this->params, true), '');
        $this->entity->pin =  rand(10000, 99999);

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return [
                    'phone' =>  $this->entity->phone,
                    'email' =>  $this->entity->email,
                    'pin' => $this->entity->pin
                ];
            } else {
                throw new Exception('Не удалось отправить код.');
            }
        }catch (Exception  $e) {
            $transaction->rollBack();

            return [];
        }

        return [];
    }

    public function removeAllOldPins(): void
    {
        $tableName = $this->entity::tableName();
        $connection = $this->entity::getDb();

        if (!empty($this->params['userId'])) {
            $connection->createCommand("
                update {$tableName}
                set is_used = 1
                where user_id = {$this->params['userId']}
                    and is_used = 0
            ")->execute();
        }

        if (!empty($this->params['phone'])) {
            $connection->createCommand("
                update {$tableName}
                set is_used = 1
                where phone = '{$this->params['phone']}'
                    and is_used = 0
            ")->execute();
        }

        if (!empty($this->params['email'])) {
            $connection->createCommand("
                update {$tableName}
                set is_used = 1
                where email = '{$this->params['email']}'
                    and is_used = 0
            ")->execute();
        }

    }

    public function removePin(): void
    {
        $tableName = $this->entity::tableName();
        $connection = $this->entity::getDb();

        if (!empty($this->params['id'])) {
            $connection->createCommand("
                update {$tableName}
                set is_used = 1
                where id = '{$this->params['id']}'
            ")->execute();
        }
    }
}