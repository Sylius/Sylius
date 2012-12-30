<?php

namespace spec\Sylius\Bundle\SalesBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Order item mapped superclass spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItem extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Entity\OrderItem');
    }

    function it_should_be_Sylius_order_item()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderItemInterface');
    }

    function it_should_extend_Sylius_order_item_model()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Model\OrderItem');
    }
}

