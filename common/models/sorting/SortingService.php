<?php

namespace common\models\sorting;

class SortingService
{
    /**
     * Сортировка массива с объектами по возрастанию вне зависимости регистра
     *
     * @param array $data
     * @param string $attr
     * @return array
     */
    public static function sortObjectByAttr(array $data, string $attr): array
    {
        usort($data, function ($object1, $object2) use ($attr) {
            return mb_strtolower($object1->$attr) <=> mb_strtolower($object2->$attr);
        });

        return $data;

    }
}