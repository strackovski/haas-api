<?php

namespace App\Service\Security\MagicLink;

/*
 * This file is part of the haas-api package.
 *
 * (c) 2019 Vladimir Strackovski <vlado@nv3.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\RouterInterface;

class Generator
{
    /**
     *
     */
    private $repository;

    /**
     * @var RouterInterface $router
     */
    private $router;

    /**
     * Generator constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository, RouterInterface $router)
    {
        $this->repository = $repository;
        $this->router = $router;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function generate(User $user)
    {
        return $this->router->generate('api.login_request', ['mLink' => $user->getMLinkHash()]);
    }
}