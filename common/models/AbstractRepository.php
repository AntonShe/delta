<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

class AbstractRepository
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    const AVAILABLE_SORTS = ['desc', 'asc', 'DESC', 'ASC'];

    protected ActiveRecord $entity;
    protected array $params = [];
    protected array $orders = ['date_updated' => 'DESC'];
    protected array $availableOrders = [];
    protected array $fieldsMap = [];
    protected array $hiddenFields = [];
    protected array $errors = [];
    protected string $alias = '';
    protected bool $withPagination = true;
    protected int $limit = 0;
    private bool $fixOrderBy = false;

    protected string $defaultOrders = '';

    public function __construct()
    {
        if (isset($this->entity)) {
            $matches = [];
            preg_match('/{{(.*)}}/', $this->entity::tableName(), $matches);
            $this->alias = $matches[1] . '.';
        }
    }
    //region Params
    protected function getSettersParamsInQuery(): array
    {
        return [
            'Simple' => [
                'params' => ['id']
            ],
            'Ids',
        ];
    }

    public function setParams(array $params): void
    {
        if (isset($params['withPagination']) && is_bool($params['withPagination'])) {
            $this->withPagination = $params['withPagination'];
        }

        if (isset($params['limit']) && $params['limit'] > 0) {
            $this->limit = $params['limit'];
        }

        $this->params = $params;
    }

    public function mergeParams(array $params): void
    {
        $this->params = array_merge(
            $this->params,
            $params
        );
    }

    public function clearParams(): void
    {
        $this->params = [];
    }

    public function getParam(string $paramName): mixed
    {
        return $this->params[$paramName] ?? null;
    }
    //endregion
    //region Orders
    public function setOrder(array $orders): void
    {
        $this->orders = $orders;
    }
    //endregion
    //region Errors
    protected function getErrors(): array
    {
        return $this->errors;
    }

    protected function setErrors(string|array $error): void
    {
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }

    protected function formatEntityErrors(array $errors): array
    {
        $output = [];
        foreach ($errors as $field => $errorArray) {
            $output[] = "Поле '{$field}': " . implode(' ', $errorArray);
        }
        return $output;
    }
    //endregion
    //region Params in query
    protected function setParamsInQuery(ActiveQuery $query): ActiveQuery
    {
        if (empty($this->params)) {
            return $query;
        }

        $query->where("1 = 1");

        foreach ($this->getSettersParamsInQuery() as $id => $data) {
            $method = 'set' . (is_array($data) ? $id : $data) . 'ParamInQuery';

            if (!method_exists($this, $method)) {
                continue;
            }

            if (is_array($data)) {
                $query = $this->$method($query, ...$data);
            } else {
                $query = $this->$method($query);
            }
        }

        return $query;
    }

    protected function setSimpleParamInQuery(ActiveQuery $query, array $params): ActiveQuery
    {
        $data = $this->convertField($this->params, true);
        foreach ($params as $param) {
            if (isset($data[$param])) {
                $query->andWhere([$this->alias . $param => $data[$param]]);
            }
        }

        return $query;
    }

    protected function setIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['id'])) {
            $query->andWhere([$this->alias . 'id' => $this->params['id']]);
        }

        return $query;
    }

    protected function setIdsParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['ids'])) {
            $query->andWhere([$this->alias . 'id' => $this->params['ids']]);
        }

        return $query;
    }

    protected function setProductSimpleSearchParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['simpleSearch'])) {
            $search = $this->params['simpleSearch'];
            $query->andWhere(['OR',
                $this->alias . "id = '{$search}'",
                $this->alias . "title LIKE '%{$search}%'",
                "REPLACE(". $this->alias . "isbn, '-','') LIKE '%{$search}%'",
                $this->alias . "labirint_id = '{$search}'",
            ]);
        }

        return $query;
    }

    protected function setPublisherSearchParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['search'])) {
            $search = addslashes($this->params['search']);

            $query->andWhere([$this->alias . 'id' => intval($search)])
                ->orWhere($this->alias . "name like '%{$search}%'");
        }

        return $query;
    }

    protected function setUserSearchParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['search'])) {
            $search = addslashes($this->params['search']);

            $query->andWhere([$this->alias . 'id' => intval($search)])
                ->orWhere($this->alias . "phone like '%{$search}%'")
                ->orWhere($this->alias . "email like '%{$search}%'")
                ->orWhere($this->alias . "first_name like '%{$search}%'")
                ->orWhere($this->alias . "second_name like '%{$search}%'")
                ->orWhere($this->alias . "last_name like '%{$search}%'");
        }

        return $query;
    }

    protected function setPersonSearchParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['search'])) {
            $search = addslashes($this->params['search']);

            $query->andWhere([$this->alias . 'id' => intval($search)])
                ->orWhere($this->alias . "name_full like '%{$search}%'")
                ->orWhere($this->alias . "name_full_ru like '%{$search}%'")
                ->orWhere($this->alias . "alternative_name like '%{$search}%'");
        }

        return $query;
    }

    protected function setSearchForPinParamInQuery(ActiveQuery $query): ActiveQuery
    {
        $isFirst = true;

        if (isset($this->params['userId'])) {
            $method = $isFirst ? 'where' : 'orWhere';
            $isFirst = $isFirst ? !$isFirst : $isFirst;

            $query->$method(['id' => $this->params['userId']]);
        }

        if (isset($this->params['phone'])) {
            $method = $isFirst ? 'where' : 'orWhere';
            $isFirst = $isFirst ? !$isFirst : $isFirst;

            $query->$method("phone like '%{$this->params['phone']}%'");
        }

        if (isset($this->params['email'])) {
            $method = $isFirst ? 'where' : 'orWhere';
            $isFirst = $isFirst ? !$isFirst : $isFirst;

            $query->$method("email like '%{$this->params['email']}%'");
        }

        return $query;
    }

    protected function setProductGenresParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['genres'])) {
            $genresString = implode(', ', $this->params['genres']);
            $query->join(
                'JOIN',
                'product_genres',
                "product_genres.genre_id IN ({$genresString}) AND product_genres.product_id = {$this->alias}id");
        }

        return $query;
    }

    protected function setProductLevelsParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['levels'])) {
            $levelsString = implode(', ', $this->params['levels']);
            $query->join(
                'JOIN',
                'product_levels',
                "product_levels.level_id IN ({$levelsString}) AND product_levels.product_id = {$this->alias}id");
        }

        return $query;
    }

    protected function setProductAgesParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['ages'])) {
            $agesString = implode(', ', $this->params['ages']);
            $query->join(
                'JOIN',
                'product_ages',
                "product_ages.age_id IN ({$agesString}) AND product_ages.product_id = {$this->alias}id");
        }

        return $query;
    }

    protected function setProductIdParamInQuery(ActiveQuery $query, $relativeField = ''): ActiveQuery
    {
        if (!empty($relativeField) && isset($this->params['productId'])) {
            $query->join(
                'JOIN',
                "product_{$relativeField}s",
                "{$relativeField}s.id = product_{$relativeField}s.{$relativeField}_id 
                    AND product_{$relativeField}s.product_id = {$this->params['productId']}"
            );
        }

        return $query;
    }

    protected function setExcludeSimpleParamInQuery(ActiveQuery $query, array $params): ActiveQuery
    {
        if (isset($this->params['exclude'])) {
            $data = $this->convertField($this->params['exclude'], true);
            foreach ($params as $param) {
                if (isset($data[$param])) {
                    $value = $data[$param];
                    if (!is_array($value)) {
                        $value = [$value];
                    }

                    $query->andWhere(['not in', $this->alias . $param, $value]);
                }
            }
        }

        return $query;
    }

    protected function setWordsParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['words'])) {
            $conditions = ['OR'];
            foreach ($this->params['words'] as $word) {
                $conditions[] = "{$this->alias}name LIKE '$word%'";
            }
            if (!empty($conditions)) {
                $query->andWhere($conditions);
            }
        }

        return $query;
    }

    protected function setIsActiveParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['isActive'])) {
            if ($this->params['isActive']) {
                $query->andWhere('start_date < current_date()');
                $query->andWhere('end_date > current_date()');
            } else {
                $query->andWhere([
                    'OR',
                    'start_date > current_date()',
                    'end_date < current_date()',
                ]);
            }
        }

        return $query;
    }
    //endregion

    //region OrderBy in query
    protected function setOrderByInQuery(ActiveQuery $query): ActiveQuery
    {
        if ($this->fixOrderBy) {
            return $query;
        }

        $orders = [];
        if ($this->defaultOrders) {
            $orders[] = $this->defaultOrders;
        }

        foreach ($this->orders as $name => $sort) {
            if (in_array($name, $this->availableOrders) && (in_array($sort, self::AVAILABLE_SORTS) || $sort === '')) {
                $orders[] = "$name $sort";
            }
        }

        $orderString = implode(',', $orders);

        if ($orderString) {
            $query->orderBy($orderString);
        }

        if (isset($this->orders['is_popular']) && $this->orders['is_popular']) {
            $query->join(
                'LEFT JOIN',
                'order_items',
                "order_items.product_id = {$this->alias}id AND order_items.date_create > DATE_ADD(now(), INTERVAL -1 YEAR)"
            );
            $query->join(
                'LEFT JOIN',
                'orders',
                "order_items.order_id = orders.id AND orders.status = 7"
            );
            $query->groupBy("{$this->alias}id");
            $query->addOrderBy("COUNT(order_items.id) {$this->orders['is_popular']}");
        }

        if (key_exists('FIELD', $this->orders)) {
            $query->addOrderBy([new Expression('FIELD (' . $this->alias . 'id, ' . implode(',', $this->orders['FIELD']) . ')')]);
        }

        return $query;
    }
    //endregion

    //region Pagination
    protected function setOffsetLimit(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->pagination)) {
            $query
                ->offset($this->pagination->getOffset())
                ->limit($this->pagination->getCountOnPage());
        }

        return $query;
    }
    //endregion

    protected function convertField(array $fields, bool $reverse = false, bool $acceptNull = false): array
    {
        $output = [];
        $fieldsMap = $reverse ? array_flip($this->fieldsMap) : $this->fieldsMap;
        $hiddenFields = $reverse ? array_flip($this->hiddenFields) : $this->hiddenFields;

        foreach ($fields as $fieldName => $fieldValue) {
            if (!isset($hiddenFields[$fieldName])) {
                if ($acceptNull) {
                    $output[$fieldsMap[$fieldName] ?? $fieldName] = $fieldValue;
                } else {
                    $output[$fieldsMap[$fieldName] ?? $fieldName] = !is_null($fieldValue) ? $fieldValue : '';
                }
            }
        }

        return $output;
    }

    protected function setProductInStockParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['inStock'])) {
            $query->distinct();
            $query->andWhere("{$this->alias}price > 0 AND {$this->alias}quantity > 0 AND {$this->alias}active = 1");
        }

        return $query;
    }
}