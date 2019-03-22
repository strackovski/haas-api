<?php

namespace App\Service\Configurator;

use App\Service\Mutator\MutatorInterface;
use App\Service\Manager\RepositoryAwareManagerInterface;
use App\Service\Primitive\StringTools;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ManagerConfigurator
 *
 * @package      App\Service\Manager\Configurator
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class ManagerConfigurator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var MutatorInterface
     */
    protected $mutator;

    /**
     * ManagerConfigurator constructor.
     *
     * @param EntityManagerInterface   $entityManager
     * @param MutatorInterface         $mutator
     * @param RouterInterface          $router
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MutatorInterface $mutator,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->entityManager = $entityManager;
        $this->mutator = $mutator;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param RepositoryAwareManagerInterface $manager
     */
    public function configure(RepositoryAwareManagerInterface $manager)
    {
        $managerClass = StringTools::classNameToClassId(get_class($manager), StringTools::LAST_HIT);
        $managerClass = StringTools::snakeToCamelCase($managerClass);

        $repositoryClass = "App\\Repository\\{$managerClass}Repository";
        $repository = new $repositoryClass($this->entityManager, $this->entityManager->getRepository("App\\Entity\\{$managerClass}"));
//        $repository->setRepository($this->entityManager->getRepository("App\\Entity\\{$managerClass}"));


        $manager->setDependencies([
            'repository' => $repository,
            'mutator' => $this->mutator,
            'router' => $this->router,
            'eventDispatcher' => $this->eventDispatcher
        ]);
    }
}
