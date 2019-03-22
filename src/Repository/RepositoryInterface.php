<?php

namespace App\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

/**
 * Interface RepositoryInterface
 *
 * @package      App\Repository\Base
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
interface RepositoryInterface
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll();

    /**
     * Finds a single entity by its ID.
     *
     * @param $id Uuid|string The entity ID.
     * @param array $criteria
     * @param array $options
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneById($id, array $criteria = [], array $options = []);

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria);

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Creates a new QueryBuilder instance that is pre-populated for this entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null);

    /**
     * @return string
     */
    public function getEntityClass();

    /**
     * @param EntityRepository|ObjectRepository $repository
     */
    public function setRepository(EntityRepository $repository): void;
}
