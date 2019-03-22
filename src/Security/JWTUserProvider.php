<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class JWTUserProvider
 *
 * @package      App\Security
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class JWTUserProvider implements UserProviderInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @param EntityManagerInterface $em
     * @param UserRepository         $repository
     */
    public function __construct(EntityManagerInterface $em, UserRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * Load user by their username or email
     *
     * @param string $userId
     *
     * @return null|object|UserInterface
     */
    public function loadUserByUsername($userId)
    {
        return $this->repository->findUser($userId);

        // Disable db check
        // $u = new User();
        // $u->setUsername($userId);
        // $u->addRole('USER');
    }

    /**
     * Refresh user - this is disabled in stateless APIs
     *
     * @param UserInterface $user
     *
     * @return UserInterface|void
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * Return the supported user class
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
