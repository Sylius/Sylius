<?php

namespace spec\Sylius\Bundle\CartBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Cart entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\Cart');
    }

    function it_should_be_Sylius_cart()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    function it_should_extend_Sylius_cart_model()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\Cart');
    }
}
