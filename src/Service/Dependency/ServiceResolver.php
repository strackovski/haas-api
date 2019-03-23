<?php

namespace App\Service\Dependency;

use App\Service\Primitive\StringTools;

/**
 * Class ServiceResolver
 *
 * @package      App\Service\Dependency
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class ServiceResolver
{
    const REPOSITORY_TYPE = 'repository';
    const MANAGER_TYPE = 'manager';

    /**
     * Returns a fully qualified service class name according to target class
     * and the service type requested: will return App\Repository\TagRepository
     * if targetClass is TagController or TagManager and the requested service
     * type is entity repository.
     *
     * @param string $targetClass The requesting class name.
     * @param string $serviceType Type of service object requested.
     *
     * @return string
     * @throws \Exception
     */
    public static function getServiceClass($targetClass, $serviceType)
    {
        $serviceClass = null;
        $serviceMap = self::getServiceMap()[$serviceType] ?? [];
        $targetClassShortName = substr($targetClass, strrpos($targetClass, '\\') + 1);

        foreach ($serviceMap['replacements'] as $needle => $replacement) {
            $targetClassShortName = str_replace(ucfirst($needle), $replacement, $targetClassShortName);
        }

        $targetClassShortName = StringTools::classNameToClassId($targetClassShortName);

        while (strpos($targetClassShortName, '_') !== false) {
            $serviceClass = sprintf($serviceMap['format'], StringTools::snakeToCamelCase($targetClassShortName));

            if (class_exists($serviceClass)) {
                return $serviceClass;
            }

            $targetClassShortName = substr($targetClassShortName, 0, strrpos($targetClassShortName, '_'));
        }

        if (class_exists(
            $serviceClass = sprintf($serviceMap['format'], StringTools::snakeToCamelCase($targetClassShortName))
        )) {
            return $serviceClass;
        }

        throw new \Exception(
            sprintf(
                "Unable to create %s for class %s in %s, service class %s does not exist.",
                $serviceType,
                $targetClass,
                self::class,
                $serviceClass
            )
        );
    }

    /**
     * The service map.
     *
     * @return array
     */
    public static function getServiceMap()
    {
        return [
            self::REPOSITORY_TYPE => [
                'format' => 'App\Repository\%sRepository',
                'fallback' => 'App\Repository\Repository',
                'replacements' => [
                    'Controller' => '',
                    'Manager' => '',
                ],
            ],
            self::MANAGER_TYPE => [
                'format' => 'App\Service\Manager\%sManager',
                'fallback' => 'App\Service\Manager\Manager',
                'replacements' => [
                    'Controller' => '',
                ],
            ],
        ];
    }
}
