<?php

namespace common\models\banner;

use common\models\AbstractDataValidator;

class BannerDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public $imageFile = null;
    public $tablet_imageFile = null;
    public $mobile_imageFile = null;
    public string $title = '';
    public ?string $text = null;
    public ?string $link = null;
    public ?int $sort = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?bool $isActive = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['title', 'text', 'link'], 'trim'],
            [['title', 'text', 'link'], 'string'],
            [['imageFile', 'tablet_imageFile', 'mobile_imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            ['sort', 'integer', 'skipOnEmpty' => true],
            [['startDate', 'endDate'], 'date', 'format' => 'php:Y-m-d'],
            ['id', 'safe'],
            ['isActive', 'boolean']
        ]);
    }
}