<?php

namespace common\models\product_image;

use common\models\AbstractRepository;

class ProductImageRepository extends AbstractRepository
{
    const IMG_SLEW_1 = 10;

    const IMG_SLEW_2 = 11;

    const IMG_SLEW_3 = 12;
    const IMG_SLEW_4 = 13;
    const IMG_SLEW_5 = 14;

    const IMG_3D_1 = 20;
    const IMG_3D_2 = 21;

    public const LIST_IMAGES = [
        self::IMG_SLEW_1 => 'ph_001.jpg',
        self::IMG_SLEW_2 => 'ph_002.jpg',
        self::IMG_SLEW_3 => 'ph_003.jpg',
        self::IMG_SLEW_4 => 'ph_004.jpg',
        self::IMG_SLEW_5 => 'ph_005.jpg',
        self::IMG_3D_1 => 'ph_01.jpg',
        self::IMG_3D_2 => 'ph_02.jpg',
    ];


    protected array $fieldsMap = [
        'type_img' => 'typeImg',
        'product_id' => 'productId',
    ];
    protected array $hiddenFields = [
        'date_created' => 'dateCreated',
        'date_updated' => 'dateUpdated'
    ];

    public function __construct()
    {
        $this->entity = new ProductImageEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'Simple' => [
                'params' => [
                    'id', 'url', 'type_img', 'product_id'
                ]
            ],
        ]);
    }
    /**
     * @return bool
     */
    public function create(): bool
    {
        try {
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();
            $this->entity->load($this->convertField($this->params, true), '');

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return false;
        }

    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        try {
            $this->entity = ProductImageEntity::findOne($this->params['id']);
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

    public function getDistinctProductsImages(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        return $query
            ->indexBy('product_id')
            ->distinct('product_id')
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function getUrlProductImages(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        return $query->select('url')
            ->asArray()
            ->all();
    }
}