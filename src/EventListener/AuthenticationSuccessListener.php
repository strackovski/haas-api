<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AuthenticationSuccessListener
 *
 * @package      App\EventListener
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class AuthenticationSuccessListener
{
    /** @var JWTEncoderInterface */
    private $jwtEncoder;

    /**
     * @param JWTEncoderInterface $jwt
     *
     * @internal param UserManager $userManager
     */
    public function __construct(JWTEncoderInterface $jwt)
    {
        $this->jwtEncoder = $jwt;
    }

    /**
     * Authentication success event handler
     *
     * User successfully logged in, generate refresh tokens
     *
     * @param AuthenticationSuccessEvent $event
     *
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException*@throws \Exception
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $refreshToken = [
            'iat' => (new \DateTime())->getTimestamp(),
            'exp' => (new \DateTime())->add(new \DateInterval('P6M'))->getTimestamp(),
            'sub' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ];

        $data['refresh_token'] = $this->jwtEncoder->encode($refreshToken);
        $data['valid_until'] = strtotime('+1 day', time());
        $data['ttl'] = 86400;

        $event->setData($data);
    }
}
