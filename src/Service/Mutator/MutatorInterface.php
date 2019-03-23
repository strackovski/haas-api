<?php

namespace App\Service\Mutator;

use App\Entity\EntityInterface;

/**
 * Interface MutatorInterface
 *
 * @package      App\Service\Mutator
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
interface MutatorInterface
{
    /**
     * Persist and flush object changes to database
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     * @throws \Exception
     */
    public function save(EntityInterface $entity): EntityInterface;

    /**
     * Flush and persist if possible (?)
     *
     * @param EntityInterface|null $entity
     *
     * @return bool
     * @throws \Exception
     */
    public function update(EntityInterface $entity = null): bool;

    /**
     * Delete entity
     *
     * @param EntityInterface $entity
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(EntityInterface $entity): bool;

    /**
     * Get managed entity class name
     *
     * @return string
     */
    public function getEntityClass(): string;
}
