<?php

namespace spec\Sylius\Bundle\SalesBundle\Builder;

use PHPSpec2\ObjectBehavior;

/**
 * Default order builder spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderBuilder extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface $itemManager
     */
    function let($itemManager)
    {
        $this->beConstructedWith($itemManager);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Builder\OrderBuilder');
    }

    function it_should_be_an_order_builder()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Builder\OrderBuilderInterface');
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_calculate_order_total($order)
    {
        $order->calculateTotal()->shouldBeCalled();

        $this->build($order);
    }
}
