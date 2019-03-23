<?php

namespace App\Controller;

use App\Entity\AccountSettings;
use App\Entity\AddressBook;
use App\Entity\EntityInterface;
use App\Entity\NotificationSettings;
use App\Entity\PrivacySettings;
use App\Entity\User;
use App\Exception\Request\NotFoundButRequiredException;
use App\Repository\AbstractRepository;
use App\Service\Dependency\ServiceResolver;
use App\Service\Form\Processor\EntityFormProcessor;
use App\Service\Manager\AbstractManager;
use App\Service\Manager\UserManager;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\FOSRestController;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AbstractRestController
 *
 * @package      App\Controller
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
abstract class AbstractRestController extends FOSRestController
{
    /** @var AbstractRepository */
    protected $repository;

    /** @var UserManager */
    protected $userManager;

    /** @var AbstractManager */
    protected $manager;

    /** @var EntityFormProcessor */
    protected $formProcessor;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var AdapterInterface */
    protected $cache;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /**
     * Request services from container once set up.
     *
     * @param ContainerInterface|null $container
     *
     * @throws \Exception
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->formProcessor = $this->get(EntityFormProcessor::class);
        $this->serializer = $this->get('serializer');

        $this->manager = $this->get(ServiceResolver::getServiceClass(get_class($this), 'manager'));
        $this->repository = $this->manager->getRepository();

        $this->cache = $this->get('cache.app');
        $this->dispatcher = $this->get('event_dispatcher');
    }

    /**
     * Fetch current user from database
     *
     * @return User|null
     */
    public function fetchUser()
    {
        $user = $this->getUser();

        if ($user instanceof JWTUserInterface) {
            if (($user = $this->repository->findUserBy(['username' => $user->getUsername()])) instanceof User) {
//                $user = $this->provisionUserSettings($user);

                return $user;
            }

            throw $this->createAccessDeniedException();
        }

        if ($user instanceof User) {
            if (!($user = $this->repository->findUserBy(['username' => $user->getUsername()])) instanceof User) {
                throw $this->createAccessDeniedException();
            }

//            $user = $this->provisionUserSettings($user);

            return $user;
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * Return JSON response
     *
     * @param       $data
     * @param int   $status
     * @param array $groups
     *
     * @return Response
     */
    protected function response($data, int $status = 200, array $groups = []): Response
    {
        return new Response(
            $this->serializer->serialize($data, 'json', ['groups' => $groups]),
            $status,
            ['Content-type' => 'application/json']
        );
    }

    /**
     * @param      $key
     * @param      $value
     *
     * @param null $entityClass
     *
     * @return EntityInterface
     * @throws NotFoundButRequiredException
     */
    protected function getEntity($key, $value, $entityClass = null)
    {
        if ($entityClass) {
            if ($e = $this->findEntity($entityClass, [$key => $value])) {
                return $e;
            }
        }

        if (!is_null($e = $this->repository->findOneBy([$key => $value]))) {
            return $e;
        }

        throw new NotFoundButRequiredException(["Entity", $key, $value], 404);
    }

    /**
     * @param       $entityClass
     * @param array $criteria
     *
     * @return EntityInterface|bool
     */
    protected function findEntity($entityClass, array $criteria = [])
    {
        foreach ($criteria as $key => $value) {
            if (is_null($key) || is_null($value)) {
                continue;
            }

            $queryString = sprintf(
            /** @lang text */
                "SELECT m FROM %s m WHERE m.%s = '%s'",
                $entityClass,
                $key,
                $value
            );

            try {
                /** @var Query $query */
                $query = $this->get("doctrine.orm.entity_manager")->createQuery($queryString);

                return $query->getSingleResult();
            } catch (\Exception $e) {
                // ...
                return false;
            }
        }

        return false;
    }

    /**
     * @param FormInterface $form
     *
     * @return array|Form
     */
    protected function getFormErrors(FormInterface $form)
    {
        $errors = [];

        if (!$form->isSubmitted()) {
            return $form;
        }

        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $paramKey => $value) {
                $template = str_replace($paramKey, $value, $template);
            }

            $errors[$key] = $template;
        }

        foreach ($form->all() as $child) {
            if ($child->isSubmitted()) {
                if (!$child->isValid()) {
                    $errors[] = [
                        'name' => $child->getName(),
                        'messages' => $this->getFormErrors($child),
                    ];
                }
            }
        }

        return $errors;
    }

    private function provisionUserSettings(User $user)
    {
        if (!$user->getPrivacy() instanceof PrivacySettings) {
            $s = new PrivacySettings();
            $user = $user->setPrivacy($s);
            $this->repository->save($s);
        }

        if (!$user->getAccount() instanceof AccountSettings) {
            $s = new AccountSettings();
            $user = $user->setAccount($s);
            $this->repository->save($s);
        }

        if (!$user->getNotificationSettings() instanceof NotificationSettings) {
            $s = new NotificationSettings();
            $user = $user->setNotificationSettings($s);
            $this->repository->save($s);
        }

        if (!$user->getAddressBook() instanceof AddressBook) {
            $a = new AddressBook($user);
            $this->repository->save($a);
        }

        return $user;
    }
}
