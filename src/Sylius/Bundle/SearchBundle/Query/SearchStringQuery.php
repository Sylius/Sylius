<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Query;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class SearchStringQuery extends Query
{

    /**
     * @var
     */
    protected $searchTerm;

    /**
     * @var
     */
    protected $searchParam;

    /**
     * @var
     */
    protected $dropdownFilterEnabled;

    /**
     * @var
     */
    protected $remoteAddress;

    /**
     * @param Request $request
     * @param         $dropdownFilterEnabled
     */
    public function __construct(Request $request, $dropdownFilterEnabled)
    {
        $requestBag = ($request->isMethod('GET'))? $request->query:$request->request;

        $this->setAppliedFilters($requestBag->get('filters'));
        $this->searchTerm = $requestBag->get('q');
        $this->searchParam = $requestBag->get('search_param');
        $this->dropdownFilterEnabled = $dropdownFilterEnabled;
        $this->remoteAddress = $request->getClientIp();
    }

    /**
     * @param $searchTerm
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return mixed
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param $searchParam
     */
    public function setSearchParam($searchParam)
    {
        $this->searchParam = $searchParam;
    }

    /**
     * @return mixed
     */
    public function getSearchParam()
    {
        return $this->searchParam;
    }

    /**
     * @return mixed
     */
    public function isDropdownFilterEnabled()
    {
        return $this->dropdownFilterEnabled;
    }

    /**
     * @return mixed
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

}
