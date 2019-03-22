<?php

namespace App\Service\Manager;

use App\Repository\RepositoryInterface;
use App\Service\Mutator\MutatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Interface RepositoryAwareManagerInterface
 *
 * @package      App\Service\Manager
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
interface RepositoryAwareManagerInterface extends ManagerInterface
{
    /**
     * @param MutatorInterface $mutator
     */
    public function setMutator(MutatorInterface $mutator): void;

    /**
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository): void;

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router): void;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

    /**
     * @param array $dependencies
     *
     * @return int
     */
    public function setDependencies(array $dependencies = []): int;
}
