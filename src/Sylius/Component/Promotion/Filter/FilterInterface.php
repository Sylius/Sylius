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

use Doctrine\Common\Collections\ArrayCollection;

interface FilterInterface
{
    /**
     * @param ArrayCollection $arrayCollection
     *
     * @return ArrayCollection
     */
    public function apply(ArrayCollection $arrayCollection);
}