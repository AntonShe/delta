<?php

namespace common\models\product;

use common\models\AbstractDTO;

class ProductDTO extends AbstractDTO
{
    public function getCourseId(): int
    {
        if (empty($course = $this->entity->course)) {
            return 0;
        }

        return $course['id'];
    }
}