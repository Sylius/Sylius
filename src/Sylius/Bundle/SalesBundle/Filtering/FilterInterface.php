<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Filtering;

/**
 * Interface for filter.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface FilterInterface
{
    function filter($filterable);
    function getCurrent();
}
