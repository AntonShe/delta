<?php

namespace common\models\search\index;

use Manticoresearch\Client;
use Manticoresearch\Index;

class ProductIndex extends AbstractIndex
{
    public const INDEX = 'products';

    public const FIELDS_WEIGHT = [
        'title' => 10,
        'annotation' => 9,
        'short_annotation' => 8,
        'delta_code' => 12,
        'isbn' => 1,
    ];

    public array $fieldsFilter = [
        'price',
        'quantity',
        'active',
        'is_new',
        'is_popular',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function createIndex(): void
    {
        $this->index->drop();
        $this->index->create([
            'title' => ['type' => 'text'],
            'isbn' => ['type' => 'text'],
            'price' => ['type' => 'integer'],
            'quantity' => ['type' => 'integer'],
            'publishing_house' => ['type' => 'string', 'options' => ['stored']],
            'annotation' => ['type' => 'text'],
            'short_annotation' => ['type' => 'text'],
            'active' => ['type' => 'bool'],
            'is_new' => ['type' => 'bool'],
            'is_popular' => ['type' => 'bool'],
            'ages' => ['type' => 'text'],
            'languages' => ['type' => 'text'],
            'genres' => ['type' => 'text'],
            'levels' => ['type' => 'text'],
            'authors' => ['type' => 'text'],
            'delta_code' => ['type' => 'text'],
            'labirint_code' => ['type' => 'text'],
        ], [
            'min_prefix_len' => '1',
            'morphology' => 'stem_enru',
            'min_stemming_len' => '4',
            'min_word_len' => '1',
            'dicr' => 'crc',
            'charset_table' => '0..9, A..Z->a..z, _, a..z, U+410..U+42F->U+430..U+44F, U+430..U+44F, U+401->U+0435, U+451->U+0435',
        ]);
    }

    /**
     * @param array $data
     * @return void
     */
    public function addOrUpdateDataToIndex(array $data): void
    {
        print_r('start foreach' . PHP_EOL);

        foreach ($data as $entity) {
            $data = [
                'title' => $entity['title'] ?? '',
                'isbn' => $entity['isbn'] ?? '',
                'price' => (int)$entity['price'],
                'quantity' => (int)$entity['quantity'],
                'is_new' => $entity['isNew'],
                'is_popular' => $entity['isPopular'],
                'active' => $entity['active'],
                'publishing_house' => $entity['publishingHouse'] ?? '',
                'annotation' => $entity['annotation'] ?? '',
                'short_annotation' => $entity['shortAnnotation'] ?? '',
                'ages' => $entity['ages'] ?? '',
                'languages' => $entity['languages'] ?? '',
                'genres' => $entity['genres'] ?? '',
                'levels' => $entity['levels'] ?? '',
                'authors' => $entity['authors'] ?? '',
                'delta_code' => (string)$entity['id'],
                'labirint_code' => (string)$entity['labirintId'],
            ];

            if ($this->index->getDocumentById($entity['id'])) {
                $this->index->replaceDocument($data, $entity['id']);
            } else {
                $this->index->addDocument($data, $entity['id']);
            }
        }
        print_r('end foreach' . PHP_EOL);
    }

    /**
     * @param array $data
     * @return void
     */
    public function updatePriceAndQuantity(array $data): void
    {
        foreach ($data as $value) {
            if ($this->index->getDocumentById($value['DeltaId'])) {
                $this->index->updateDocument([
                    'price' => $value['price'],
                    'quantity' => $value['quantity']
                ], $value['DeltaId']);
            }
        }
    }
}