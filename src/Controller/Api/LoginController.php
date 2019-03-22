<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class LoginController
 *
 * @package      App\Controller\Api
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class LoginController extends Controller
{
    /**
     * The security layer will intercept this request
     *
     * @Route("/login")
     * @Method({"POST"})
     *
     * @return Response
     */
    public function getToken()
    {
        return new Response('', 401);
    }

    /**
     * The security layer will intercept this request
     *
     * @Route("/refresh_token")
     * @Method({"POST"})
     *
     * @return Response
     */
    public function refreshToken()
    {
        return new Response('', 401);
    }
}
