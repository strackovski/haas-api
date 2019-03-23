<?php

namespace App\Service\Primitive;

/**
 * Class StringTools
 *
 * @package      App\Service\Primitive
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class StringTools
{
    const FIRST_HIT = 'first';
    const LAST_HIT = 'last';

    /**
     * Convert camelCase to snake_case
     *
     * @param      $input
     * @param bool $capitalizeFirst
     *
     * @return mixed|string
     */
    public static function snakeToCamelCase($input, $capitalizeFirst = true)
    {
        $str = str_replace('_', '', ucwords($input, '_'));

        if (!$capitalizeFirst) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * Convert 'Fully\Qualified\ClassName' to 'class_name'
     * or 'class' when $stripResultAfterChar is '_'.
     *
     * @param string $className
     * @param bool   $stripResultAfterChar Character after which (first occurrence) to strip result
     * @param bool   $keepNamespace
     *
     * @return bool|string
     */
    public static function classNameToClassId($className, $stripResultAfterChar = false, $keepNamespace = false)
    {
        $className = $keepNamespace ? $className : substr($className, strrpos($className, '\\'));
//        $id = StringTools::camelToSnakeCase(substr($className, strrpos($className, '\\')));
        $id = StringTools::camelToSnakeCase($className);

        return $stripResultAfterChar ? substr(
            $id,
            0,
            $stripResultAfterChar === self::LAST_HIT ? strrpos($id, '_') : strpos($id, '_')
        ) : $id;
    }

    /**
     * Convert snake_case to camelCase
     *
     * @param $input
     *
     * @return string
     */
    public static function camelToSnakeCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}
