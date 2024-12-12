<?php

namespace common\models\description;

use common\models\AbstractRepository;
use common\models\IRepository;
use common\models\Pagination;
use http\Exception\InvalidArgumentException;
use http\Exception\RuntimeException;
use Yii;
use yii\base\Exception;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use JetBrains\PhpStorm\ArrayShape;

class DescriptionRepository extends AbstractRepository implements IRepository
{
    private Pagination $pagination;
    private string $filePath;

    public function __construct()
    {
        $this->entity = new DescriptionEntity();
        $this->pagination = Pagination::getInstance();
        $this->filePath = 'uploads/marketing/descriptions';
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function read(int $fieldID): array
    {
        return [
            $this->entity::findOne($fieldID)
        ];
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'pagination'    => "array",
        'descriptions'  => "array|\yii\db\ActiveRecord[]"
    ])]
    public function readPage(int $pageNumber = 1): array
    {
        $query = $this->entity::find();

        $this->pagination->setTotalCount($query->count());

        $descriptions = $query
            ->offset($this->pagination->getOffset())
            ->limit($this->pagination->getCountOnPage())
            ->asArray()
            ->all();

        return [
            'pagination'    => $this->pagination->getData(),
            'descriptions'  => $descriptions
        ];
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success'       => 'bool',
        'description'   => 'array',
        'errors'        => 'array'
    ])]
    public function create(array $attributes): array
    {
        $this->entity->scenario = 'inserting';
        $this->entity->setAttributes($attributes);

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($this->entity->validate()) {
                $image = UploadedFile::getInstance($this->entity, 'image');
                $uniqueFilename = uniqid();

                BaseFileHelper::createDirectory($this->filePath);

                if ($image->saveAs($this->filePath . $uniqueFilename . '.' . $image->extension)) {
                    $this->entity->setAttribute(
                        'image',
                        $uniqueFilename . '.' . $image->extension
                    );
                    $this->entity->save(false);
                    $transaction->commit();

                    return [
                        'success' => true,
                        'description' => $this->entity->toArray()
                    ];
                }

                $transaction->rollBack();
                throw new RuntimeException(
                    sprintf(
                        'Не удалось сохранить изображение (%s) \n
                        по указанному пути: %s',
                        $image,
                        $this->filePath . $uniqueFilename . '.' . $image->extension
                    )
                );
            }
        } catch (Exception $commitOrCreateDirException) {
            $this->setErrors($commitOrCreateDirException->getMessage());
            $transaction->rollBack();
        } catch (RuntimeException $saveImageException) {
            $this->setErrors($saveImageException->getMessage());
            $transaction->rollBack();
        }

        return [
            'success' => false,
            'errors' => $this->entity->getErrors()
        ];
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success'       => "bool",
        'description'   => "array",
        'errors'        => 'array'
    ])]
    public function update(array $attributes): array
    {
        $description = $this->entity::findOne($attributes['id']);

        try {
            if (is_null($description)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Не удалось найти описание с указанным ID. \n
                        Переданный ID: %s ',
                        $attributes['id']
                    )
                );
            }

            $attributes['date_updated'] = date('Y-m-d H:i:s');
            $description->setAttributes($attributes);

            if ($description->save()) {
                return [
                    'success'   => true,
                    'description' => $description->toArray()
                ];
            }
        } catch (\InvalidArgumentException $nonExistentID) {
            $this->setErrors($nonExistentID->getMessage());
        }

        return [
            'success'   => false,
            'errors'    => $this->entity->getErrors()
        ];
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success'       => 'bool',
        'description'   => 'array',
        'errors'        => 'array'
    ])]
    public function delete(int $fieldID): array
    {
        $description = $this->entity::findOne($fieldID);

        try {
            if (is_null($description)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Не удалось найти описание с указанным ID. \n
                        Переданный ID: %s',
                        $fieldID
                    )
                );
            }

            if ($description->save()) {
                return [
                    'success'   => true,
                    'description' => $description->toArray()
                ];
            }

            throw new \http\Exception\RuntimeException(
                sprintf(
                    'Не удалось пометить акцию (%s) удаленной. \n',
                    $fieldID
                )
            );
        } catch (\InvalidArgumentException $nonExistentDescriptionID) {
            $this->setErrors($nonExistentDescriptionID->getMessage());
        } catch (\RuntimeException $saveFieldException) {
            $this->setErrors($saveFieldException->getMessage());
        }

        return [
            'success'   => false,
            'errors'    => $this->entity->getErrors()
        ];
    }
}