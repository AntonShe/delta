<?php

namespace common\models\promotion;

use common\models\AbstractDataValidator;

class PromotionDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public $imageFile = null;
    public $tablet_imageFile = null;
    public $mobile_imageFile = null;
    public string $title = '';
    public ?string $annotation = null;
    public ?string $link = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?bool $isActive = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['title', 'annotation', 'link'], 'trim'],
            [['title', 'annotation', 'link'], 'string'],
            [['imageFile', 'tablet_imageFile', 'mobile_imageFile'],
                'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['startDate', 'endDate'], 'date', 'format' => 'php:Y-m-d'],
            ['id', 'safe'],
            ['isActive', 'boolean']
        ]);
    }
}