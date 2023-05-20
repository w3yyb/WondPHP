<?php
 
function env($key, $default = null)
    {
        $apcu_key="env$key";
        if (apcu_exists($apcu_key)) {
            $apcu_value= apcu_fetch($apcu_key);

        if ($apcu_value === false) {
            return $default;
        }
            switch (strtolower($apcu_value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
                    default:
                    return $apcu_value;
            }
        }
       
        $value = getenv($key);
        apcu_store($apcu_key, $value, 60);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = \strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
?>
