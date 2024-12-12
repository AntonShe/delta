<?php

namespace common\models\promotion;

use common\models\AbstractDTO;

class PromotionDTO extends AbstractDTO
{
    public function getImage(): string
    {
        return $this->formatImage($this->entity->image);
    }

    public function getTabletImage(): string
    {
        return $this->formatImage($this->entity->tabletImage);
    }

    public function getMobileImage(): string
    {
        return $this->formatImage($this->entity->mobileImage);
    }

    public function getDateValid(): string
    {
        return "С {$this->entity->startDate} по {$this->entity->endDate}";
    }

    private function formatImage(string $fileName): string
    {
        return PromotionRepository::IMAGE_PATH . '/' . $fileName;
    }
}