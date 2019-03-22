<?php

namespace App\Repository;

use App\Entity\User;
use App\Exception\Request\NotFoundButRequiredException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\Uuid;

/**
 * Class UserRepository
 *
 * @package      App\Repository
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class UserRepository extends AbstractRepository
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

    public function findSettings()
    {

    }

    /**
     * @param $email
     *
     * @return User
     * @throws NotFoundButRequiredException
     */
    public function findByEmail($email)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        try {
            $r = $qb->getQuery()->getOneOrNullResult();
            if (is_null($r)) {
                throw new NotFoundButRequiredException(['User', 'id or wallet address', $email]);
            }
        } catch (NonUniqueResultException $e) {
            throw $e;
        }

        return $r;
    }

    /**
     * @return array
     */
    public function getUserIds()
    {
        $users = $this->repository->findAll();
        $result = [];

        /** @var User $user */
        foreach ($users as $user) {
            $result[] = $user->getEmail();
        }

        return $result;
    }

    /**
     * @param string|null $mLink
     *
     * @return User|null
     */
    public function findUserByMagicLink(?string $mLink = null) {
        if (is_null($mLink)) {
            return null;
        }

        try {
            $now = new \DateTime();

            return $this->createQueryBuilder('u')
                        ->select('u')
                        ->andWhere('u.mLinkHash = :mLink')
                        ->andWhere('u.mLinkValidUntil > :validUntil')
                        ->setParameter('mLink', $mLink)
                        ->setParameter('validUntil', $now)
                        ->getQuery()
                        ->getOneOrNullResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param      $id
     *
     * @param null $with
     * @return mixed
     */
    public function findUser($id, $with = null)
    {
        if ($with) {
            if ($with === 'own') {
                try {
                    return $this->createQueryBuilder('u')
                        ->select('u, a, w, p, x, n')
                        ->leftJoin('u.addressBook', 'a')
                        ->leftJoin('u.wallet', 'w')
                        ->leftJoin('u.privacy', 'p')
                        ->leftJoin('u.account', 'x')
                        ->leftJoin('u.notificationSettings  ', 'n')
                        ->andWhere('u.username = :id')
                        ->orWhere('u.email = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
                } catch (NonUniqueResultException $e) {
                    return null;
                }
            } elseif ($with === 'transactions') {
                try {
                    return $this->createQueryBuilder('u')
                        ->select('u, a, w, t')
                        ->leftJoin('u.addressBook', 'a')
                        ->leftJoin('u.wallet', 'w')
                        ->leftJoin('u.transactions', 't')
                        ->andWhere('u.username = :id')
                        ->orWhere('u.email = :id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->getOneOrNullResult();
                } catch (NonUniqueResultException $e) {
                    return null;
                }
            }
        } else {
            try {
                return $this->createQueryBuilder('u')
                    ->select('u, a, w, t')
                    ->leftJoin('u.addressBook', 'a')
                    ->leftJoin('u.wallet', 'w')
                    ->leftJoin('u.transactions', 't')
                    ->andWhere('u.username = :id')
                    ->orWhere('u.email = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param $criteria
     *
     * @return mixed
     */
    public function search($criteria)
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.username) LIKE :username')
            ->orWhere('LOWER(u.email) LIKE :email')
            ->setParameter('username', '%' . strtolower($criteria) . '%')
            ->setParameter('email', '%' . strtolower($criteria) . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $criteria
     *
     * @return mixed
     */
    public function searchExact($criteria)
    {
        return $this->createQueryBuilder('u')
                    ->where('LOWER(u.username) LIKE :username')
                    ->orWhere('LOWER(u.email) LIKE :email')
                    ->setParameter('username', strtolower($criteria))
                    ->setParameter('email', strtolower($criteria))
                    ->getQuery()
                    ->getResult();
    }


    /**
     * @param $id
     *
     * @return User
     * @throws NonUniqueResultException
     * @throws NotFoundButRequiredException
     */
    public function findByIdOrWalletAddress($id)
    {
        if (!Uuid::isValid($id)) {
            $qb = $this->createQueryBuilder('u')
                ->select('u')
                ->leftJoin('u.wallet', 'w')
                ->where('w.address = :address')
                ->setParameter('address', $id);
        } else {
            $qb = $this->createQueryBuilder('u')
                ->select('u')
                ->where('u.id = :id')
                ->setParameter('id', $id);
        }

        try {
            $r = $qb->getQuery()->getOneOrNullResult();
            if (is_null($r)) {
                throw new NotFoundButRequiredException(['User', 'id or wallet address', $id]);
            }
        } catch (NonUniqueResultException $e) {
            throw $e;
        }

        return $r;
    }
}
