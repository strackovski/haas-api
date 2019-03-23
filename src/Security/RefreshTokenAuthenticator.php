<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

/**
 * Class RefreshTokenAuthenticator
 *
 * @package      App\Security
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class RefreshTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface,
                                           AuthenticationSuccessHandlerInterface
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var LoginSuccessHandler */
    private $successHandler;

    /** @var AuthenticationFailureHandlerInterface */
    private $failureHandler;

    /** @var JWTEncoderInterface */
    private $jwtEncoder;

    /**
     * @param EntityManagerInterface                $em
     * @param JWTEncoderInterface                   $jwt
     * @param AuthenticationSuccessHandlerInterface $successHandler
     * @param AuthenticationFailureHandlerInterface $failureHandler
     */
    public function __construct(
        EntityManagerInterface $em,
        JWTEncoderInterface $jwt,
        AuthenticationSuccessHandlerInterface $successHandler,
        AuthenticationFailureHandlerInterface $failureHandler = null
    ) {
        $this->em = $em;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->jwtEncoder = $jwt;
    }

    /**
     * @param Request $request
     * @param         $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        $refreshToken = false;
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $refreshToken = $data['refresh_token'];
        }

        if (!$refreshToken) {
            // Force to fail with authentication
            // If we need to skip it and try another provider just return null
            throw new BadCredentialsException('No Refresh token found');
        }

        return new PreAuthenticatedToken(
            'anon.', $refreshToken, $providerKey
        );
    }

    /**
     * Verify given refresh token against a user provider and provider key
     *
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param                       $providerKey
     *
     * @return PreAuthenticatedToken
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof RefreshTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of RefreshTokenProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $refreshToken = $token->getCredentials();
        $decodedRefreshToken = $this->jwtEncoder->decode($refreshToken);

        if (!isset($decodedRefreshToken['username'])) {
            throw new AuthenticationException(
                sprintf('Refresh token "%s" is invalid.', $refreshToken)
            );
        }

        $user = $userProvider->loadUserByUsername($decodedRefreshToken['username']);

        return new PreAuthenticatedToken(
            $user, $refreshToken, $providerKey, $user->getRoles()
        );
    }

    /**
     * Check if token is supported
     *
     * @param TokenInterface $token
     * @param                $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * Handle authentication failure event
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = $this->failureHandler->onAuthenticationFailure($request, $exception);
        if (!$response instanceof Response) {
            throw new \RuntimeException('Authentication Failure Handler did not return a Response.');
        }

        return $response;
    }

    /**
     * Handle authentication success event
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $request->attributes->set('refresh', true);

        $response = $this->successHandler->onAuthenticationSuccess($request, $token);
        if (!$response instanceof Response) {
            throw new \RuntimeException('Authentication Success Handler did not return a Response.');
        }

        return $response;
    }
}
