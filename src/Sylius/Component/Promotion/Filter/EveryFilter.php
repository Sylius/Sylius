<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * @author Piotr Walków <walkow.piotr@gmail.com>
 * @author Pete Ward <peter.ward@reiss.com>
 */
class EveryFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected function filter(Collection $collection)
    {
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveConfiguration(array $configuration)
    {
        return $configuration;
    }
}
