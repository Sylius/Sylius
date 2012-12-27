<?php

namespace spec\Sylius\Bundle\CartBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Default cart entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultCart extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\DefaultCart');
    }

    function it_should_be_Sylius_cart()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    function it_should_extend_Sylius_cart_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\Cart');
    }
}
