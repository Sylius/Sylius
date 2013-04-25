<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Cart spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Entity\Cart');
    }

    function it_implements_Sylius_cart_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    function it_extends_Sylius_cart_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\Cart');
    }

    function it_implements_shippables_aware_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface');
    }

}
