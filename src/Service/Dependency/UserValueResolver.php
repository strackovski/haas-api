<?php

namespace App\Service\Dependency;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserValueResolver
 *
 * @package App\ArgumentResolver
 * @author  Vladimir Strackovski <vladimir.strackovski@gmail.com>
 */
class UserValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UserValueResolver constructor.
     *
     * @param Security       $security
     * @param UserRepository $repository
     */
    public function __construct(Security $security, UserRepository $repository)
    {
        $this->security = $security;
        $this->repository = $repository;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if (User::class !== $argument->getType()) {
            return false;
        }

        return $this->security->getUser() instanceof User;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {

        if ($request->getPathInfo() === "/me/settings") {
            yield $this->repository->findUser($this->security->getUser()->getUsername(), 'own');

            return;
        }

        if ($request->getPathInfo() === "/transactions") {
            yield $this->repository->findUser($this->security->getUser()->getUsername(), 'transactions');

            return;
        }

        yield $this->repository->findUser($this->security->getUser()->getUsername());
    }
}