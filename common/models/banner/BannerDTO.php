<?php

namespace common\models\banner;

use common\models\AbstractDTO;

class BannerDTO extends AbstractDTO
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

    private function formatImage(string $fileName): string
    {
        return BannerRepository::IMAGE_PATH . $fileName;
    }
}