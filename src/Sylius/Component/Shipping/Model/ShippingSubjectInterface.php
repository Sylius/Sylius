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
    public function getShippingUnitCount();

    /**
     * @return int
     */
    public function getShippingUnitTotal();

    /**
     * @return Collection|ShippableInterface[]
     */
    public function getShippables();
}
