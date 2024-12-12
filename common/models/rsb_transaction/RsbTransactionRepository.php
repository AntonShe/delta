<?php

namespace common\models\rsb_transaction;

use common\models\AbstractRepository;
use yii\db\ActiveQuery;
use yii\db\Exception;

class RsbTransactionRepository extends AbstractRepository
{
    /**
     * Оплата заказа;
     */
    const TYPE_PAYMENT_PAID = 1;
    /**
     * Возврат заказа;
     */
    const TYPE_PAYMENT_REFUND = 2;
    /**
     * Транзакция зарегистрирована в системе;
     */
    const STATUS_CREATED = 1;
    /**
     * Выполнение транзакции продолжается;
     */
    const STATUS_PENDING = 2;
    /**
     * Транзакция завершена успешно;
     */
    const STATUS_SUCCEEDED = 3;

    /**
     * Транзакция отклонена системой RSB_ECOMM;
     * Неуспешная транзакция;
     * Транзакция отменена;
     * Транзакция автоматически отменена системой RSB_ECOMM
     * Время отведенное на проведение транзакции истекло
     */
    const STATUS_CANCELED = 4;


    protected array $fieldsMap = [
        'transaction_id' => 'transactionId',
        'order_number' => 'orderNumber',
        'uniq_id' => 'uniqId',
        'user_id' => 'userId',
        'client_ip_addr' => 'clientIpAddr'
    ];

   public function __construct()
   {
       $this->entity = new RsbTransactionEntity();
       parent::__construct();
   }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => [
                    'transaction_id', 'order_number', 'amount', 'user_id', 'uniq_id', 'status', 'type', 'client_ip_addr'
                ]
            ],
            'ExcludeSimple' => [
                'params' => ['status']
            ],
            'DateCreatedRange'
        ]);
    }


    public function create(): bool
    {
        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();
        try {
            $this->entity->load($this->convertField($this->params, true), '');

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }

    }

    /**
     * @param int $id
     * @return bool
     */
    public function update(int $id): bool
    {
        try {
            $this->entity = RsbTransactionEntity::findOne($id);
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            $this->entity->setAttributes($this->convertField($this->params, true));

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }

        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            return false;
        }
    }

    public function getStatus(string $status): int
    {
        return match ($status) {
            'OK' => self::STATUS_SUCCEEDED,
            'CREATED' => self::STATUS_CREATED,
            'DECLINED', 'FAILED', 'REVERSED', 'AUTOREVERSED', 'TIMEOUT' => self::STATUS_CANCELED,
            'PENDING' => self::STATUS_PENDING,
        };
    }


    /**
     * @param bool $isOne
     * @return array|null
     */
    public function getTransactions(bool $isOne = false): array|null
    {
        $query = $this->setParamsInQuery($this->entity::find());

        $trans = $query
            ->asArray();

        return $isOne ? $trans->one() : $trans->all();
    }

    public function setDateCreatedRangeParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['dateCreatedRange'])) {
            $dateEnd = date('Y-m-d', strtotime(date('Y-m-d') . '-2 days'));
            $query->andWhere("date_created BETWEEN '$dateEnd' AND NOW()");
        }

        return $query;
    }
}