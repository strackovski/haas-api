<?php

namespace App\Controller\Api;

use App\Controller\AbstractRestController;
use App\Entity\Cheer;
use App\Entity\User;
use App\Event\MagicLinkRequestedEvent;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\Mailer;
use \Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 *
 * @package      App\Controller\Api
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class UserController extends AbstractRestController
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var Mailer
     */
    private $mailer;

    const MLINK_NO_USER_PWD = true;

    /**
     * @param ContainerInterface|null $container
     *
     * @throws \Exception
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->formFactory = $this->get('form.factory');
        $this->userManager = $this->get('fos_user.user_manager');
        $this->dispatcher = $this->get('event_dispatcher');
        $this->tokenGenerator = $this->get('fos_user.util.token_generator.default');
        $this->mailer = $this->get('app.custom_fos_user_mailer');
    }

    /**
     * @param UserInterface $user
     * @param Request       $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAll(UserInterface $user, Request $request)
    {
        return $this->response($this->repository->aggregateData(), 200, ['public']);
    }

    /**
     * @param               $userId
     * @param UserInterface $user
     * @param Request       $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCheer($userId, UserInterface $user, Request $request)
    {
        $forUser = $this->repository->findOneBy(['id' => $userId]);

        if (is_null($forUser)) {
            throw $this->createNotFoundException();
        }

        $c = new Cheer($forUser);
        $this->repository->save($c);


        return $this->response($c, 200, ['public']);
    }

    /**
     * @param         $mlinkHash
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function magicLinkRedirect($mlinkHash, Request $request) {
        return $this->redirect(sprintf("haas://magic-link/%s", $mlinkHash));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function magicLinkRequestAction(Request $request)
    {
        if (!($username = $request->request->get('email', false))) {
            return $this->response(["error" => "Unauthorized."], 401);
        }

        if (is_null($user = $this->repository->findUserBy(['email' => $username]))) {
            return $this->response(["error" => "Unauthorized."], 401);
        }

        $user->setMLinkHash(md5(time()));
        $user->setMLinkValidUntil((new \DateTime())->add(new \DateInterval("PT2H")));
        $this->repository->save($user);
        $this->dispatcher->dispatch(MagicLinkRequestedEvent::NAME, new MagicLinkRequestedEvent($user));

        return $this->response(["status" => "OK", "hash" => $user->getMLinkHash()], 200);
    }

    /**
     * @param Request                  $request
     *
     * @param JWTTokenManagerInterface $JWTmanager
     * @param JWTEncoderInterface      $JWTEncoder
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Exception
     * @Rest\View()
     * @Rest\Post("/auth/login")
     */
    public function magicLinkLoginAction(Request $request, JWTTokenManagerInterface $JWTmanager, JWTEncoderInterface $JWTEncoder)
    {
        $data = json_decode($request->getContent(), true);

        if (!array_key_exists('mlink', $data)) {
            return $this->response('Error', 401);
        }

        if (is_null($user = $this->repository->findUserByMagicLink($data['mlink']))) {
            return $this->response('Error', 401);
        }


        $refreshToken = [
            'iat' => (new \DateTime())->getTimestamp(),
            'exp' => (new \DateTime())->add(new \DateInterval('P6M'))->getTimestamp(),
            'sub' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        try {
            return $this->response(
                [
                    'token' => $JWTmanager->create($user),
                    'email' => $user->getEmail(),
                    'refresh_token' => $JWTEncoder->encode($refreshToken),
                    'valid_until' => strtotime('+1 day', time()),
                    'ttl' => 86400,
                ],
                200
            );
        } catch (\Exception $e) {
            return $this->response('Error', 401);
        }
    }

    /**
     * Register a new user account
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param Request                      $request
     *
     * @return mixed
     *
     * @Rest\View()
     * @Rest\Post("/register")
     * @throws \Exception
     */
    public function registerAction(UserPasswordEncoderInterface $encoder, Request $request)
    {
        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setEnabled(false);

        $event = new GetResponseUserEvent($user, $request);
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createNamed(
            'user',
            'App\Form\RegistrationType',
            $user,
            ['validation_groups' => ['Registration', 'Default'], 'csrf_protection' => false]
        );

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                $user->addRole('ROLE_USER');

//                if (self::MLINK_NO_USER_PWD == true) {
//                    $plainPassword = sha1(mt_rand(0, 12));
//                    $user->setEnabled(true);
//                    $user->setMLinkHash(md5(time()));
//                    $user->setMLinkValidUntil((new \DateTime())->add(new \DateInterval("PT2H")));
//
//                    $this->dispatcher->dispatch(MagicLinkRequestedEvent::NAME, new MagicLinkRequestedEvent($user));
//
//                } else {
//                    $plainPassword = $data['plainPassword'];
//                }

                $plainPassword = $data['plainPassword'];

                $user->setPassword($encoder->encodePassword($user, $plainPassword));
                try {
                    $this->userManager->updateUser($user);
                } catch (UniqueConstraintViolationException $e) {
                    $detail = null;
                    if (preg_match("/DETAIL:([\s\S]*)/", $e->getMessage(), $match)) {
                        if (count($match) > 0) {
                            $detail = trim($match[1]);
                        }
                    }

                    return $this->response(
                        [
                            "error" => "A conflict occurred: unique constraint violation has blocked user update.",
                            "detail" => $detail // just print the extract instead of the whole query
                        ],
                        409
                    );
                }

                $this->dispatcher->dispatch(
                    FOSUserEvents::REGISTRATION_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $event->getResponse())
                );

                $this->manager->provisionUserDefaults($user);

                return $this->response($user, 200, ['settings', 'user_basic']);
            }

            $event = new FormEvent($form, $request);
            $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $this->response($this->getFormErrors($form), 400);
            }
        }

        return $this->response($form, 400);
    }

    /**
     *
     * @param Request $request
     *
     * @return mixed
     *
     * @Rest\Post("/password/request")
     * @throws \Exception
     */
    public function requestResetPasswordAction(Request $request)
    {
        $username = $request->request->get('username');

        $user = $this->userManager->findUserByUsernameOrEmail($username);

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $this->response($event->getResponse()->getContent(), 400);
        }

        if (null !== $user && !$user->isPasswordRequestNonExpired(60)) {
            $event = new GetResponseUserEvent($user, $request);
            $this->dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $this->response($event->getResponse()->getContent(), 401);
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $event = new GetResponseUserEvent($user, $request);
            $this->dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $this->response($event->getResponse()->getContent(), 402);
            }

            $this->mailer->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);

            $event = new GetResponseUserEvent($user, $request);
            $this->dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $this->response($event->getResponse()->getContent(), 403);
            }
        }

        return $this->response([], 200);
    }

    /**
     *
     * @param Request $request
     *
     * @param         $token
     *
     * @return mixed
     *
     * @Rest\Post("/password/reset")
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw $this->createNotFoundException();
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                return $this->response([], 200);
            }

            $this->dispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $this->response([], $response->getStatusCode());
        }

        return $this->response($this->getFormErrors($form), 200);
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return null|\Symfony\Component\Form\FormInterface|\Symfony\Component\HttpFoundation\Response
     *
     * @Rest\Get("/me/settings")
     */
    public function getProfileAction(User $user, Request $request)
    {
        return $this->response($user, 200, ['settings', 'user_profile_public']);
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return null|\Symfony\Component\Form\FormInterface|\Symfony\Component\HttpFoundation\Response
     *
     * @Rest\Patch("/me/settings")
     */
    public function editProfileAction(User $user, Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var FormFactory $formFactory */
        $formFactory = $this->get('form.factory');

        $form = $formFactory->createNamed(
            'app_profile_edit',
            'App\Form\ProfileType',
            $user,
            ['validation_groups' => ['Default']]
        );

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
            $userManager->updateUser($user);

            return $this->response($user, 200, ['settings', 'personal']);
        }

        return $this->response($form, 400);
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return null|\Symfony\Component\Form\FormInterface|\Symfony\Component\HttpFoundation\Response
     *
     * @Rest\Get("/users/search")
     */
    public function searchAction(User $user, Request $request)
    {
        if (!$request->query->get('criteria', false)) {
            return $this->response([], 200, ['user_profile_public']);
        }

        $users = $this->repository->search($request->query->get('criteria', null));

        return $this->response($users, 200, ['user_profile_public', 'public']);
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return null|\Symfony\Component\Form\FormInterface|\Symfony\Component\HttpFoundation\Response
     *
     * @Rest\Get("/users/find")
     */
    public function findAction(User $user, Request $request)
    {
        if (!$request->query->get('criteria', false)) {
            return $this->response([], 200, ['user_profile_public']);
        }

        $users = $this->repository->searchExact($request->query->get('criteria', null));

        return $this->response($users, 200, ['user_profile_public', 'public']);
    }

    /**
     * @return null|\Symfony\Component\Form\FormInterface|\Symfony\Component\HttpFoundation\Response
     *
     * @Rest\Get("/register/confirm")
     */
    public function confirmAction()
    {
        return $this->response([], 200);
    }

    /**
     * @return null|\Symfony\Component\Form\FormInterface|\Symfony\Component\HttpFoundation\Response
     *
     * @Rest\Get("/register/check")
     */
    public function checkEmailAction()
    {
        return $this->response([], 200);
    }
}
