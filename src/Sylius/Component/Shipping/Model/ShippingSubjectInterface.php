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
 * Shipping subject.
 *
 * This interface can be implemented by any object, which needs to be
 * evaluated by default shipping calculators and rule checkers.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingSubjectInterface
{
    /**
     * Get the total weight of shipped goods.
     *
     * @return int
     */
    public function getShippingWeight();

    /**
     * Get the total volume of shipped goods.
     *
     * @return int
     */
    public function getShippingVolume();

    /**
     * Get the total amount of shipped goods.
     *
     * @return int
     */
    public function getShippingItemCount();

    /**
     * Get the total value of shipped goods.
     *
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
