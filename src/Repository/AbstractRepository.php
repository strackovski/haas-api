<?php

namespace App\Repository;

use App\Entity\Cheer;
use App\Entity\EntityInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Class AbstractRepository
 *
 * @package      App\Repository
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AbstractRepository constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EntityRepository|null  $repository
     */
    public function __construct(EntityManagerInterface $entityManager, ?EntityRepository $repository = null)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function aggregateData() {
        return [
//            "users" => $this->findEntity(User::class),
            "users" => $this->getUsers(),
            "cheers"=> $this->getCheers()
        ];
    }

    /**
     * @return mixed
     */
    public function getCheers() {
        return $this->entityManager->getRepository(Cheer::class)->findAll();
    }

    /**
     * @return mixed
     */
    public function getUsers() {
        return $this->createQueryBuilder('u')
                    ->select('u')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param       $id
     * @param array $criteria
     * @param array $options
     *
     * @return object|null
     */
    public function findOneById($id, array $criteria = [], array $options = [])
    {
        return $this->repository->findOneById($id, $criteria, $options);
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->repository->getClassName();
    }

    /**
     * @param EntityRepository $repository
     */
    public function setRepository(EntityRepository $repository): void
    {
        // TODO: Implement setRepository() method.
    }

    /**
     * Find user helper
     *
     * @param array $criteria
     *
     * @return null|User
     */
    public function findUserBy(array $criteria)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy($criteria);
    }

    /**
     * @param EntityInterface $entity
     */
    public function persist(EntityInterface $entity) {
        $this->entityManager->persist($entity);
    }

    /**
     * @param       $entity
     * @param array $criteria
     *
     * @return object|null
     */
    public function findEntityBy($entity, array $criteria)
    {
        return $this->entityManager->getRepository($entity)->findOneBy($criteria);
    }

    /**
     * @param       $entity
     * @param array $criteria
     *
     * @return object|null
     */
    public function findEntity($entity)
    {
        return $this->entityManager->getRepository($entity)->findAll();
    }

    /**
     * @param       $entityClass
     * @param array $criteria
     *
     * @return mixed|bool
     */
    public function exists($entityClass, array $criteria = [])
    {
        foreach ($criteria as $key => $value) {
            if (is_null($key) || is_null($value)) {
                continue;
            }

            try {
                $query = $this->entityManager
                    ->createQuery("SELECT m FROM $entityClass m WHERE m.$key = '$value'");
                return $query->getSingleResult();
            } catch (NonUniqueResultException $e) {
                continue;
            } catch (NoResultException $e) {
                continue;
            }
        }

        return false;
    }

    /**
     * @param $entityClass
     *
     * @return array
     */
    public function fetchAll($entityClass)
    {
        return $this->entityManager->getRepository($entityClass)->findAll();
    }

    /**
     * @param $entity
     *
     * @return mixed
     */
    public function save($entity = null)
    {
        if (!is_null($entity)) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return $entity;
        }

        $this->entityManager->flush();

        return null;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->repository->getClassName();
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return null|object
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param null       $limit
     * @param null       $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param      $id
     * @param null $lockMode
     * @param null $lockVersion
     *
     * @return null|object
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->repository->find($id, $lockMode, $lockVersion);
    }

    /**
     * @param      $alias
     * @param null $indexBy
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        return $this->repository->createQueryBuilder($alias, $indexBy);
    }
}
