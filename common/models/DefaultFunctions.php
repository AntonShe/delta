<?php

namespace common\models;

use Exception;

class DefaultFunctions
{
    private static ?DefaultFunctions $instance = null;
    
    public static function getInstance(): DefaultFunctions
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){}

    private function __clone(){}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function wordEnding($value, $count1, $count2, $count5)
    {
        if ($value === 0) {
            return $count5;
        }
        
        $n1 = $value % 100;
        $n2 = $value % 10;

        if ($n1 >= 11 && $n1 <= 19) {
            return $count5;
        } else if ($n2 >= 2 && $n2 <= 4) {
            return $count2;
        } else if ($n2 === 1) {
            return $count1;
        }
        
        return $count5;
    }

    public function nullToEmpty($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_array($item) && !empty($item)) {
                    $value[$key] = $this->nullToEmpty($item);
                } else {
                    $value[$key] = $item === null ? '' : $item;
                }
            }
            return $value;
        } else {
            return '';
        }
    }
}