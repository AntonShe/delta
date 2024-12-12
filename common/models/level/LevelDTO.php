<?php

namespace common\models\level;

use common\models\AbstractDTO;
use yii\helpers\Html;

class LevelDTO extends AbstractDTO
{
    public function getName(): string
    {
        return Html::encode($this->entity->name);
    }
}