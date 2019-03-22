<?php

namespace App\Service\Dependency;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Security;

/**
 * Class RequestQueryResolver
 *
 * @package App\ArgumentResolver
 * @author  Vladimir Strackovski <vladimir.strackovski@gmail.com>
 */
class RequestQueryResolver
{
    public function __construct()
    {
    }
}