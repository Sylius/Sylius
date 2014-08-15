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
    private $searchTerm;

    /**
     * @var
     */
    private $searchParam;

    /**
     * @var
     */
    private $dropdownFilterEnabled;

    /**
     * @var
     */
    private $remoteAddress;

    /**
     * @param Request $request
     * @param         $dropdownFilterEnabled
     */
    public function __construct(Request $request, $dropdownFilterEnabled)
    {
        $this->setAppliedFilters(($request->isMethod('GET'))? $request->query->get('filters'):$request->request->get('filters'));
        $this->searchTerm = ($request->isMethod('GET'))? $request->query->get('q'):$request->request->get('q');
        $this->searchParam = ($request->isMethod('GET'))? $request->query->get('search_param'):$request->request->get('search_param');
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
