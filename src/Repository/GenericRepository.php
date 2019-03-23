<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class GenericRepository
 *
 * @package      App\Repository
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class GenericRepository extends AbstractRepository
{
    /**
     * SubscriptionRepository constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repository = $entityManager->getRepository(User::class);
    }
}
