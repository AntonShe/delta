<?php

namespace common\models\level;

use common\models\AbstractDTO;

class LevelService
{
    protected LevelRepository $levelRepository;

    public function __construct()
    {
        $this->levelRepository = new LevelRepository();
    }

    public function setParams($params): void
    {
        $this->levelRepository->setParams($params);
    }

    public function getLevels(): array
    {
        return $this->levelRepository->getLevels();
    }

    public function getLevelsByCatalog(): array
    {
        $list = $this->levelRepository->getLevels();
        foreach ($list as &$item) {
            $item = LevelDTO::make($item);
        }
        return $list;
    }
}