<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * Class AccessDeniedHandler
 *
 * @package App\Security
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /** @var SessionInterface */
    protected $session;

    /**
     * AccessDeniedHandler constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        throw $accessDeniedException;
    }
}
