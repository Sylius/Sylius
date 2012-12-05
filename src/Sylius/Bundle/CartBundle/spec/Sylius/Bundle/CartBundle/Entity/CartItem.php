<?php

namespace spec\Sylius\Bundle\CartBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Cart item entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItem extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\CartItem');
    }

    function it_should_be_Sylius_cart_item()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartItemInterface');
    }

    function it_should_extend_Sylius_cart_item_model()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\CartItem');
    }
}
