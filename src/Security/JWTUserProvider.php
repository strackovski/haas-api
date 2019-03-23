<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param EntityManagerInterface $em
     * @param UserRepository         $repository
     */
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->em = $em;
        $this->repository = $repository;
        $this->encoder = $encoder;
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
        return $this->repository->findOneBy(['username' => $userId]);
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
