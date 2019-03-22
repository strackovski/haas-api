<?php

namespace App\Service\Primitive;

/**
 * Class ArrayTools
 *
 * @package      App\Service\Primitive
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class ArrayTools
{
    /**
     * Find an array in a multidimensional array by key
     *
     * @param array $haystack The array to search in
     * @param string $searchKey The key name to search for
     * @param string $searchValue The value of the key
     * @return array
     */
    public static function findArrayByKeyValue(array $haystack, string $searchKey, string $searchValue)
    {
        if (empty($haystack)) {
            return [];
        }

        $result = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($haystack));

        foreach ($haystack as $subArray) {
            $subArrayIterator = $iterator->getSubIterator();
            if ($subArrayIterator[$searchKey] === $searchValue) {
                $result[] = iterator_to_array($subArrayIterator);
            }
        }

        return $result;
    }

    /**
     * Search for similar text in array
     *
     * @param $haystack
     * @param $term
     * @return array
     */
    public static function likeSearchInArray($haystack, $term)
    {
        $result = [];

        foreach ($haystack as $key => $value) {
            if (false !== stripos(strtolower($value), strtolower($term))) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
