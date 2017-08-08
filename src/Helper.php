<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 16-May-17
 * Time: 12:01 PM
 */

namespace Rashidul\RainDrops;


class Helper
{

    public function returnIfExists($array, $key)
    {
        return array_key_exists($key, $array) ? $array[$key] : '';
    }

    public static function getLastKey($arr)
    {
        end($arr);

        return key($arr);
    }

    /*
     * Inserts a new key/value before the key in the array.
     *
     * @param $key
     *   The key to insert before.
     * @param $array
     *   An array to insert in to.
     * @param $new_key
     *   The key to insert.
     * @param $new_value
     *   An value to insert.
     *
     * @return
     *   The new array if the key exists, FALSE otherwise.
     *
     * @see array_insert_after()
     */
    public static function array_insert_before($key, array &$array, $new_key, $new_value) {
        if (array_key_exists($key, $array)) {
            $new = array();
            foreach ($array as $k => $value) {
                if ($k === $key) {
                    $new[$new_key] = $new_value;
                }
                $new[$k] = $value;
            }
            return $new;
        }
        return FALSE;
    }

    /*
     * Inserts a new key/value after the key in the array.
     *
     * @param $key
     *   The key to insert after.
     * @param $array
     *   An array to insert in to.
     * @param $new_key
     *   The key to insert.
     * @param $new_value
     *   An value to insert.
     *
     * @return
     *   The new array if the key exists, old array otherwise.
     *
     * @see array_insert_before()
     */
    public static function array_insert_after($key, array &$array, $new_key, $new_value) {
        if (array_key_exists ($key, $array)) {
            $new = array();
            foreach ($array as $k => $value) {
                $new[$k] = $value;
                if ($k === $key) {
                    $new[$new_key] = $new_value;
                }
            }
            return $new;
        }
        return $array;
    }
}