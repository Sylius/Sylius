<?php

namespace spec\Sylius\Bundle\SalesBundle\EventDispatcher\Event;

use PHPSpec2\ObjectBehavior;

/**
 * Filter order even spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterOrderEvent extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function let($order)
    {
        $this->beConstructedWith($order);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent');
    }

    function it_should_be_an_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
    }

    function it_should_return_assigned_order($order)
    {
        $this->getOrder()->shouldReturn($order);
    }
}
