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


/**
 * @author agounaris <agounaris@gmail.com>
 */
class SearchStringQuery extends Query
{

    /* @var */
    private $searchTerm;

    /* @var */
    private $searchParam;

    /* @var */
    private $dropdownFilterEnabled;

    /**
     * @param $searchTerm
     * @param $searchParam
     * @param $appliedFilters
     * @param $dropdownFilterEnabled
     */
    public function __construct($searchTerm, $searchParam, $appliedFilters, $dropdownFilterEnabled)
    {
        parent::setAppliedFilters($appliedFilters);
        $this->searchTerm = $searchTerm;
        $this->searchParam = $searchParam;
        $this->dropdownFilterEnabled = $dropdownFilterEnabled;
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

}
