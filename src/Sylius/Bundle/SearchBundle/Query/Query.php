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
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class Query
{
    /**
     * @var array
     */
    protected $appliedFilters = [];

    /**
     * @param array $appliedFilters
     */
    public function setAppliedFilters(array $appliedFilters)
    {
        $this->appliedFilters = $appliedFilters;
    }

    /**
     * @return array
     */
    public function getAppliedFilters()
    {
        return $this->appliedFilters;
    }
}
