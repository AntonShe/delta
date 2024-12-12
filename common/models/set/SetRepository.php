<?php

namespace common\models\set;

use common\models\AbstractRepository;
use common\models\Pagination;

class SetRepository extends AbstractRepository
{
    private Pagination $pagination;

    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];

    public function __construct()
    {
        $this->entity = new SetEntity();
        $this->pagination = Pagination::getInstance();

        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => [
                    'id'
                ]
            ],
        ]);
    }

    public function getSets(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
        }


        return [
            'sets' => $query->asArray()->all(),
            'pagination' => $this->pagination->getData()
        ];
    }

    /**
     * @return array
     */
    public function getGenres(): array
    {
        try {
            return (array)$this->entity::getDb()->createCommand("
                SELECT genre_id
                FROM sets_genres gs
                WHERE set_id = {$this->params['id']}
            ")->queryColumn();
        } catch (\Exception) {}

        return [];
    }
}