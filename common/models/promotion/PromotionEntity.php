<?php

namespace common\models\promotion;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class PromotionEntity extends ActiveRecord
{
    public UploadedFile $imageFile;
    public UploadedFile $tablet_imageFile;
    public UploadedFile $mobile_imageFile;

    public static function tableName(): string
    {
        return '{{promotions}}';
    }

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        return [
            [['title', 'annotation', 'link'], 'string'],
            [['title', 'annotation', 'link'], 'trim'],
            ['is_active', 'boolean'],
            [['image', 'tablet_image', 'mobile_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['date_created', 'date_updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            ['date_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => ['create', 'update']],
            [['start_date', 'end_date'], 'default', 'value' => date('Y-m-d'), 'on' => 'create'],
        ];
    }

    public function upload(string $fieldName, string $fileName): bool
    {
        if ($this->validate() && $this->{"{$fieldName}File"}->saveAs("img/promotions/$fileName", false)) {
            if ($this->$fieldName) {
                unlink("img/promotions/{$this->$fieldName}");
            }
            return true;
        } else {
            return false;
        }
    }

    public function beforeDelete()
    {
        unlink("img/promotions/{$this->image}");
        unlink("img/promotions/{$this->tablet_image}");
        unlink("img/promotions/{$this->mobile_image}");

        return parent::beforeDelete();
    }
}