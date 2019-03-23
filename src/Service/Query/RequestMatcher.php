<?php

namespace App\Service\Query;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestMatcher
 *
 * @package Service\Query
 * @author  Vladimir Strackovski <vladimir.strackovski@gmail.com>
 */
class RequestMatcher
{
    /**
     * Tries find a controller from request's path.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param Request $request
     *
     * @return void An array of parameters
     *
     */
    public function matchRequest(Request $request)
    {
        // @todo
    }

}