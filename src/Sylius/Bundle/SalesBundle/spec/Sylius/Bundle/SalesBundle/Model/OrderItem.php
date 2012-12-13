<?php

namespace spec\Sylius\Bundle\SalesBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Order item spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItem extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Model\OrderItem');
    }

    function it_should_be_Sylius_order_item()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderItemInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_have_quantity_equal_to_1_by_default()
    {
        $this->getQuantity()->shouldReturn(1);
    }

    function its_quantity_should_be_mutable()
    {
        $this->setQuantity(8);
        $this->getQuantity()->shouldReturn(8);
    }

    function it_should_have_unit_price_equal_to_0_by_default()
    {
        $this->getUnitPrice()->shouldReturn(0);
    }

    function it_should_have_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_should_complain_when_quantity_is_less_than_1()
    {
        $this
            ->shouldThrow(new \OutOfRangeException('Quantity must be greater than 0'))
            ->duringSetQuantity(-5)
        ;
    }

    function its_total_should_be_mutable()
    {
        $this->setTotal(59.99);
        $this->getTotal()->shouldReturn(59.99);
    }

    function it_should_calculate_correct_total()
    {
        $this->setQuantity(13);
        $this->setUnitPrice(14.99);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(194.87);
    }
}
