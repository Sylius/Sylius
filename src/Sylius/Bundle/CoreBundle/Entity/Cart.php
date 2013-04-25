<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CartBundle\Entity\Cart as BaseCart;
use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;

/**
 * Cart entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends BaseCart implements ShippablesAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function getShippables()
    {
        $shippables = new ArrayCollection();

        foreach ($this->items as $item) {
            $shippables->add($item->getVariant());
        }

        return $shippables;
    }
}
