<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\CartItem');
    }

    function it_implements_Sylius_cart_item_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartItemInterface');
    }

    function it_extends_Sylius_order_item()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Model\OrderItem');
    }
}
