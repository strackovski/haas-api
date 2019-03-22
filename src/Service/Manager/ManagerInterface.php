<?php

namespace App\Service\Manager;

use App\Entity\EntityInterface;

/**
 * Interface ManagerInterface
 *
 * @package      App\Service\Manager
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
interface ManagerInterface
{
    /**
     * @param EntityInterface ...$entities
     *
     * @return EntityInterface
     */
    public function create(EntityInterface ...$entities) : EntityInterface;

    /**
     * @return mixed
     */
    public function getEntityClass();
}
