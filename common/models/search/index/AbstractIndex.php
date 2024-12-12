<?php

namespace common\models\search\index;

use Manticoresearch\Client;
use Manticoresearch\Index;
use Manticoresearch\Search;

abstract class AbstractIndex
{
    public Index $index;
    protected const FIELDS_WEIGHT = [];
    public const INDEX = '';

    protected array $params = [];

    protected array $fieldsFilter = [];
    protected const KEYBOARD_LAYOUT_RU_EN = [
        'й' => 'q', 'ц' => 'w', 'у' => 'e', 'к' => 'r', 'е' => 't', 'н' => 'y', 'г' => 'u', 'ш' => 'i', 'щ' => 'o',
        'з' => 'p', 'х' => '[', 'ъ' => ']', 'ф' => 'a', 'ы' => 's', 'в' => 'd', 'а' => 'f', 'п' => 'g', 'р' => 'h',
        'о' => 'j', 'л' => 'k', 'д' => 'l', 'ж' => ';', 'э' => '\'', 'я' => 'z', 'ч' => 'x', 'с' => 'c', 'м' => 'v',
        'и' => 'b', 'т' => 'n', 'ь' => 'm', 'б' => ',', 'ю' => '.','Й' => 'Q', 'Ц' => 'W', 'У' => 'E', 'К' => 'R',
        'Е' => 'T', 'Н' => 'Y', 'Г' => 'U', 'Ш' => 'I', 'Щ' => 'O', 'З' => 'P', 'Х' => '[', 'Ъ' => ']', 'Ф' => 'A',
        'Ы' => 'S', 'В' => 'D', 'А' => 'F', 'П' => 'G', 'Р' => 'H', 'О' => 'J', 'Л' => 'K', 'Д' => 'L', 'Ж' => ';',
        'Э' => '\'', '?' => 'Z', 'Ч' => 'X', 'С' => 'C', 'М' => 'V', 'И' => 'B', 'Т' => 'N', 'Ь' => 'M', 'Б' => ',',
        'Ю' => '.'
    ];

    protected const KEYBOARD_LAYOUT_EN_RU = [
        'q' => 'й', 'w' => 'ц', 'e' => 'у', 'r' => 'к', 't' => 'е', 'y' => 'н', 'u' => 'г', 'i' => 'ш', 'o' => 'щ',
        'p' => 'з', '[' => 'х', ']' => 'ъ', 'a' => 'ф', 's' => 'ы', 'd' => 'в', 'f' => 'а', 'g' => 'п', 'h' => 'р',
        'j' => 'о', 'k' => 'л', 'l' => 'д', ';' => 'ж', '\'' => 'э', 'z' => 'я', 'x' => 'ч', 'c' => 'с', 'v' => 'м',
        'b' => 'и', 'n' => 'т', 'm' => 'ь', ',' => 'б', '.' => 'ю','Q' => 'Й', 'W' => 'Ц', 'E' => 'У', 'R' => 'К',
        'T' => 'Е', 'Y' => 'Н', 'U' => 'Г', 'I' => 'Ш', 'O' => 'Щ', 'P' => 'З', '[' => 'Х', ']' => 'Ъ', 'A' => 'Ф',
        'S' => 'Ы', 'D' => 'В', 'F' => 'А', 'G' => 'П', 'H' => 'Р', 'J' => 'О', 'K' => 'Л', 'L' => 'Д', ';' => 'Ж',
        '\'' => 'Э', 'Z' => '?', 'X' => 'ч', 'C' => 'С', 'V' => 'М', 'B' => 'И', 'N' => 'Т', 'M' => 'Ь', ',' => 'Б',
        '.' => 'Ю'
    ];
    const LIST_KEYBOARD_LAYOUT = [
        self::KEYBOARD_LAYOUT_RU_EN,
        self::KEYBOARD_LAYOUT_EN_RU,
    ];
    abstract public function createIndex();

    abstract public function addOrUpdateDataToIndex(array $data);

    public function __construct()
    {
        $this->index = new Index(new Client([
            'host' => \Yii::$app->params['manticore_host'],
            'port'=> \Yii::$app->params['manticore_port_http']
        ]),static::INDEX);
    }

    /**
     * @return void
     */
    public function runOptimize(): void
    {
        $this->index->optimize(true);
    }

    /**
     * @param Search $search
     * @return Search
     */
    public function setLimitInQuery(Search $search): Search
    {
        $search->limit($search->get()->getTotal())->maxMatches($search->get()->getTotal());

        if (isset($this->params['limit'])) {
            $search->limit($this->params['limit']);
        }

        return $search;
    }

    /**
     * @return string
     */
    protected function getSearchString(): string
    {
        $searchFields = "@(" . implode(',', array_keys(static::FIELDS_WEIGHT)) . ") ";
        return $searchFields . '*' . $this->params['search'] . '*';
    }


    /**
     * @param string $text
     * @param int $arrow
     * @return string
     */
    protected function correctLangText(string $text, int $arrow = 0): string
    {
        if (in_array($text[0], self::LIST_KEYBOARD_LAYOUT[0])) {
            $arrow = 1;
        }

        return strtr(
            $text,
            self::LIST_KEYBOARD_LAYOUT[$arrow] ?? array_merge(self::LIST_KEYBOARD_LAYOUT[0], self::LIST_KEYBOARD_LAYOUT[1])
        );
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        $query = $this->setFilterInQuery();

        if (!$query->limit(1)->get()->count()) {
            $this->params['search'] = $this->correctLangText($this->params['search']);
            $query = $this->setFilterInQuery();
        }

        $query = $this->setLimitInQuery($query);
        $result = $query->get();

        $ids = [];
        foreach ($result as $value) {
            $ids[] = $value->getId();
        }

        return $ids;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }


    /**
     * @return Search
     */
    public function setFilterInQuery(): Search
    {
        $search = $this->index->search($this->getSearchString())
            ->match($this->getSearchString())
            ->option('field_weights', static::FIELDS_WEIGHT)
            ->option('ranker', 'sph04');

        foreach ($this->params as $key => $value) {
            if (in_array($key, $this->fieldsFilter)) {
                if (is_array($value)) {
                    $search->filter($key, 'range', $value);
                } else {
                    $search->filter($key, 'equals', $value);
                }
            } elseif ($key == 'exclude') {
                if (is_array($value)) {
                    $search->notFilter($key, 'range', $value);
                } else {
                    $search->notFilter($key, 'equals', $value);
                }
            }
        }

        return $search;
    }

}