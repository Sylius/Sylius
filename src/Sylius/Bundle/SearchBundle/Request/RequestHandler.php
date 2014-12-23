<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request handling
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class RequestHandler
{
    /**
     * @var ParameterBag
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request->isMethod('GET') ? $request->query : $request->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request->isMethod('GET') ? $request->query : $request->request;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->request->get('page', 1);
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->request->get('q');
    }

    /**
     * @return mixed
     */
    public function getSearchParam()
    {
        return $this->request->get('search_param');
    }
}
