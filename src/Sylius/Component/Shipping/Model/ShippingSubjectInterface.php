<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;

/**
 * This interface can be implemented by any object, which needs to be
 * evaluated by default shipping calculators and rule checkers.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingSubjectInterface
{
    /**
     * @return int
     */
    public function getShippingWeight();

    /**
     * @return int
     */
    public function getShippingVolume();

    /**
     * @return int
     */
    public function getShippingItemCount();

    /**
     * @return int
     */
    public function getShippingItemTotal();

    /**
     * Get collection of unique shippables.
     *
     * @return Collection|ShippableInterface[]
     */
    public function getShippables();
}
