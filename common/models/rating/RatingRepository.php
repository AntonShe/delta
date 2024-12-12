<?php

namespace common\models\rating;

use common\models\AbstractRepository;
use Yii;


class RatingRepository extends AbstractRepository
{
    protected array $fieldsMap = [
        'user_id' => 'userId',
        'product_id' => 'productId',
    ];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];

    public function __construct()
    {
        $this->entity = new RatingEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => ['user_id', 'product_id']
            ],
        ]);
    }

    public function setUserIdInParams(): void
    {
        $this->params['userId'] = \Yii::$app->user->id;
    }

    public function getRatingByProduct(): array
    {
        $rating = $this->setParamsInQuery($this->entity::find())->sum('value');
        $countVotes = $this->setParamsInQuery($this->entity::find())->count();

        return [
            'count' => $countVotes,
            'value' => $countVotes > 0 ?
                str_replace(
                    '.0',
                    '',
                    number_format($rating / $countVotes, 1, '.', '')
                ) :
                0,
        ];
    }

    public function getRatingByProductAndUser(): int
    {
        if (!Yii::$app->user->isGuest) {
            return (int)$this->setParamsInQuery($this->entity::find())->select('value')->scalar();
        }

        return 0;
    }

    public function create(): array
    {
        try {
            $this->entity->setScenario(self::SCENARIO_CREATE);
            $this->entity->load($this->convertField($this->params, true), '');

            if (!$this->entity->validate()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Создание не удалось');
            }

            $this->entity->save();
        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    public function update(int $id): array
    {
        try {
            $this->entity = RatingEntity::findOne($id);
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $this->entity->setAttributes($this->convertField($this->params, true));

            if (!$this->entity->validate()) {
                $this->setErrors($this->formatEntityErrors($this->entity->getErrors()));
                throw new \Exception('Обновление не удалось');
            }
            $this->entity->save();

        } catch (\Exception $e) {
            $this->setErrors($e->getMessage());
        }

        if ($errors = $this->getErrors()) {
            return [
                'errors' => $errors
            ];
        }

        return [
            'result' => true
        ];
    }

    public function delete(int $id): array
    {
        $this->entity->setScenario(self::SCENARIO_DELETE);
        return [];
    }

    public function checkIsExist(): bool|int
    {
        $id = $this->setParamsInQuery($this->entity::find())
            ->select('id')
            ->scalar();

        if ($id) {
            return $id;
        }
        return false;
    }
}