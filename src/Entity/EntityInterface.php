<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * Interface EntityInterface
 *
 * @package      App\Entity
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
interface EntityInterface
{
    /**
     * @return \Ramsey\Uuid\Uuid
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getModifiedAt();

    /**
     * @return mixed
     */
    public function getCreatedAt();
}
