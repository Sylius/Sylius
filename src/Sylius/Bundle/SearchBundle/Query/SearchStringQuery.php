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
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchStringQuery extends Query
{
    /**
     * @var string
     */
    protected $searchTerm;

    /**
     * @var
     */
    protected $searchParam;

    /**
     * @var bool
     */
    protected $dropdownFilterEnabled;

    /**
     * @var string
     */
    protected $remoteAddress;

    /**
     * @param Request $request
     * @param bool    $dropDownFilterEnabled
     */
    public function __construct(Request $request, $dropDownFilterEnabled = false)
    {
        $requestBag = $request->isMethod('GET') ? $request->query : $request->request;

        $this->setAppliedFilters($requestBag->get('filters', array()));
        $this->searchTerm = $requestBag->get('q');
        $this->searchParam = $requestBag->get('search_param');
        $this->dropdownFilterEnabled = (bool) $dropDownFilterEnabled;
        $this->remoteAddress = $request->getClientIp();
    }

    /**
     * @param string $searchTerm
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return string
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
     * @return bool
     */
    public function isDropdownFilterEnabled()
    {
        return $this->dropdownFilterEnabled;
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }
}
