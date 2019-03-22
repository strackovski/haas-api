<?php

namespace App\Service\Manager;

use App\Entity\EntityInterface;
use App\Entity\PrivacySettings;
use App\Entity\User;
use App\Service\Mutator\MutatorInterface;
use App\Repository\RepositoryInterface;
use App\Service\Primitive\StringTools;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Entity\AccountSettings;
use App\Entity\AddressBook;
use App\Entity\NotificationSettings;

/**
 * Class AbstractManager
 *
 * @package      App\Service\Manager
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
abstract class AbstractManager implements RepositoryAwareManagerInterface
{
    /**
     * @var MutatorInterface
     */
    protected $mutator;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    abstract public function create(EntityInterface ...$entities): EntityInterface;

    public function updateStatus(EntityInterface $entity, User $user = null, $status = 'seen')
    {
        return null;
    }

    public function save(EntityInterface $entity)
    {
        try {
            return $this->mutator->save($entity);
        } catch (\Exception $e) {
            // $this->logger->
            // exception message builder
            throw $e;
        }
    }

    public function update(EntityInterface $entity = null)
    {
        try {
            $this->mutator->update($entity);
        } catch (\Exception $e) {
            // ...
        }
    }

    public function delete(EntityInterface $entity)
    {
        try {
            $this->mutator->delete($entity);
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws \Exception
     */
    public function provisionUserDefaults(User $user)
    {
        if (!$user->getPrivacy() instanceof PrivacySettings ) {
            $s = new PrivacySettings();
            $user = $user->setPrivacy($s);
            $this->save($s);
        }

        if (!$user->getAccount() instanceof AccountSettings) {
            $s = new AccountSettings();
            $user = $user->setAccount($s);
            $this->save($s);
        }

        if (!$user->getNotificationSettings() instanceof NotificationSettings) {
            $s = new NotificationSettings();
            $user = $user->setNotificationSettings($s);
            $this->save($s);
        }

        if (!$user->getAddressBook() instanceof AddressBook) {
            $s = new AddressBook($user);
            $this->save($s);
        }

        return $user;
    }

    /**
     * Returns the class name of the object managed by the manager.
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return "App\\Entity\\" . StringTools::classNameToClassId($this, true);
    }

    /**
     * @param MutatorInterface $mutator
     */
    public function setMutator(MutatorInterface $mutator): void
    {
        $this->mutator = $mutator;
    }

    /**
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return MutatorInterface
     */
    public function getMutator(): MutatorInterface
    {
        return $this->mutator;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     *
     *
     * @param array $dependencies Array of property => dependency class records.
     *
     * @return int Count of dependencies set
     */
    public function setDependencies(array $dependencies = []): int
    {
        $count = 0;
        foreach ($dependencies as $property => $dependency) {
            if (property_exists(get_class($this), $property)) {
                $this->$property = $dependency;
                $count++;
            }
        }

        return $count;
    }
}
