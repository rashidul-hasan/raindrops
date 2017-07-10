<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 08-Jul-17
 * Time: 6:59 PM
 */

namespace Rashidul\RainDrops\Generator;


class Helper
{

    // https://stackoverflow.com/questions/24316347/how-to-format-var-export-to-php5-4-array-syntax
    public function arrayAsString($array, $indent = "\t")
    {
        switch (gettype($array)) {
            case "string":
                return '"' . addcslashes($array, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($array) === range(0, count($array) - 1);
                $r = [];
                foreach ($array as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : $this->arrayAsString($key) . " => ")
                        . $this->arrayAsString($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $array ? "TRUE" : "FALSE";
            default:
                return var_export($array, TRUE);
        }

    }


}