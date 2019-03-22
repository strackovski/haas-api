<?php

namespace App\Service\Query;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestMatcher
 *
 * @package Service\Query
 * @author  Vladimir Strackovski <vladimir.strackovski@gmail.com>
 */
class EntityResolver
{
    const ENTITY_NAMESPACE = "App\\Entity\\";

    const ANNOTATIONS = [
        OneToOne::class,
        OneToMany::class,
        ManyToOne::class,
        ManyToMany::class
    ];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var bool
     */
    private $restful;

    public function __construct(Request $request, bool $restful = false)
    {
        $this->request = $request;
        $this->restful = $restful;
    }

    /**
     * Tries to resolve entity class from request to map its associations with property names.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param Request $request
     */
    public function getClassName(Request $request)
    {

    }

    /**
     * @param Request $request
     *
     * @return \ReflectionClass|null
     */
    protected function processRequest(Request $request)
    {
        $route = strtolower($request->attributes->get("_route"));
        $candidate = strtolower(
            substr(
                $ctrl = $request->attributes->get("_controller"),
                strrpos($ctrl, "\\") + 1,
                strpos($ctrl, "Controller")
            )
        );

        if (!is_null($entity = $this->entityExists($candidate))) {
            return $entity;
        }

        if (array_search($candidate, $ex = explode("_", $route))) {
            if (in_array($candidate . "s", explode("_", $route))) {
                return $this->entityExists($candidate);
            }
        }

        return null;
    }

    /**
     * @param string $className
     *
     * @return null|\ReflectionClass
     */
    protected function entityExists(string $className): ?\ReflectionClass
    {
        try {
            return new \ReflectionClass(self::ENTITY_NAMESPACE . "$className");
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return array|null
     * @throws AnnotationException
     */
    protected function filterAnnotations(\ReflectionClass $reflection)
    {
        $filtered = [];
        foreach ($reflection->getProperties() as $property) {
            $reader = new AnnotationReader();
            $annotation = $reader->getPropertyAnnotation($property, Annotation::class);

            if (!in_array(get_class($annotation), self::ANNOTATIONS)) {
                continue;
            }

            $filtered[$property->getName()] = [
                'entity' => $annotation->targetEntity,
                'fetch' => $annotation->fetch,
                'type' => get_class_methods($annotation)
            ];
        }

        return $filtered ?? null;
    }

}