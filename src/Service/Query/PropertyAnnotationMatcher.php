<?php

namespace App\Service\Query;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Class PropertyAnnotationMatcher
 *
 * @package Service\Query
 * @author  Vladimir Strackovski <vladimir.strackovski@gmail.com>
 */
class PropertyAnnotationMatcher
{
    /**
     * Tries to resolve an entity association target class from a s
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param \ReflectionProperty $property
     */
    public function matchAnnotation(\ReflectionProperty $property)
    {

        try {
            $reader = new AnnotationReader();
        } catch (AnnotationException $e) {
        }
        $myAnnotation = $reader->getPropertyAnnotation($property, Annotation::class);

        if ($myAnnotation instanceof OneToOne) {
            // @todo
        }
    }

}