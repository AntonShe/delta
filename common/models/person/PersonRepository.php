<?php

namespace common\models\person;

use common\models\AbstractRepository;
use common\models\Pagination;

class PersonRepository extends AbstractRepository
{
    protected Pagination $pagination;
    protected array $fieldsMap = [
        'name_full' => 'nameFull',
        'name_full_ru' => 'nameFullRu',
        'alternative_name' => 'alternativeName',
        'seo_title' => 'seoTitle',
        'seo_meta_keywords' => 'seoMetaKeywords',
        'seo_meta_description' => 'seoMetaDescription',
        'labirint_id' => 'labirintId',
    ];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];

    public function __construct()
    {
        $this->entity = new PersonEntity();
        $this->pagination = Pagination::getInstance();

        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'ProductId' => [
                'relativeField' => 'person',
            ],
            'Simple' => [
                'params' => [
                    'id', 'name_full', 'name_full_ru', 'alternative_name', 'description', 'seo_title', 'seo_meta_keywords',
                    'cover', 'seo_meta_description', 'active', 'labirint_id'
                ],
            ],
            'PersonSearch'
        ]);
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        $this->entity->setScenario(self::SCENARIO_CREATE);
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
     * @return bool
     */
    public function update(): bool
    {
        try {
            $this->entity->setScenario(self::SCENARIO_UPDATE);
            $this->entity = PersonEntity::findOne($this->params['id']);
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

    /**
     * @return array
     */
    public function getPersons(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
        }

        $persons = $query
            ->asArray()
            ->all();

        foreach ($persons as &$person) {
            $person = $this->convertField($person);
            $person['name'] = $person['nameFull'] ?: $person['nameFullRu'];
        }

        return [
            'persons' => $persons,
            'pagination' => $this->pagination->getData()
        ];
    }

    /**
     * @return array
     */
    public function getProductIds(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        if (isset($this->params['personId'])) {
            $query->select('*');
            $query->join(
                'JOIN',
                'product_persons',
                "product_persons.person_id = {$this->alias}id AND product_persons.person_id = {$this->params['personId']}");
            $query->join(
                'JOIN',
                'products',
                "products.id = product_persons.product_id AND products.active = 1 AND products.is_new = 0"
            );
        }

        return $query->asArray()->all();
    }

    /**
     * @return bool|int
     */
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

    /**
     * @return int
     */
    public function getLastId(): int
    {
        return $this->entity->id;
    }
}