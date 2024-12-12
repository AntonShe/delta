<?php

namespace common\models\search\index;

class UserIndex extends AbstractIndex
{
    public const INDEX = 'users';

    public const FIELDS_WEIGHT = [
        'fio' => 10,
        'email' => 9,
        'phone' => 8,
        'delta_code' => 12
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
            'fio' => ['type' => 'text'],
            'email' => ['type' => 'text'],
            'phone' => ['type' => 'text'],
            'delta_code' => ['type' => 'text'],
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
                'fio' => $entity['fio'] ? trim($entity['fio']) : '',
                'email' => $entity['email'] ?? '',
                'phone' => $entity['phone'] ?? '',
                'delta_code' => (string)$entity['id'],
            ];

            if ($this->index->getDocumentById($entity['id'])) {
                $this->index->replaceDocument($data, $entity['id']);
            } else {
                $this->index->addDocument($data, $entity['id']);
            }
        }
        print_r('end foreach' . PHP_EOL);
    }
}