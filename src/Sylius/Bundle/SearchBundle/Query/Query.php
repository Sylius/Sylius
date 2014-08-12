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
class Query
{

    /* @var */
    private $appliedFilters;

    /**
     * @param $appliedFilters
     */
    public function setAppliedFilters($appliedFilters)
    {
        $this->appliedFilters = $appliedFilters;
    }

    /**
     * @return mixed
     */
    public function getAppliedFilters()
    {
        return $this->appliedFilters;
    }

} 