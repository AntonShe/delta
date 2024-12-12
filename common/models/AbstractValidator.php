<?php

namespace common\models;

use yii\base\Model;

class AbstractValidator extends Model
{
    public function getData(): array
    {
        $output = [];
        foreach ($this->toArray() as $name => $value) {
            if (!in_array($value, $this->getIgnoringValues(), true)) {
                $output[$name] = $value;
            }
        }
        return $output;
    }

    protected function getIgnoringValues(): array
    {
        return [];
    }

    #region default validators
    public function validateIntArray($attribute, $params)
    {
        foreach ($this->$attribute as $id) {
            if (!is_numeric($id)) {
                $this->addError($attribute, 'ID в списке должны быть числами');
                return;
            }
        }
    }

    public function validateStringArray($attribute, $params)
    {
        foreach ($this->$attribute as $string) {
            if (!is_string($string)) {
                $this->addError($attribute, 'Должны быть строками');
                return;
            }
        }
    }
    #endregion
}