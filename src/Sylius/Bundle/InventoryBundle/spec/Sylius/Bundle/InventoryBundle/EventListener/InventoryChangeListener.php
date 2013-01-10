<?php

namespace spec\Sylius\Bundle\InventoryBundle\EventListener;

use PHPSpec2\ObjectBehavior;

/**
 * Inventory change listener spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryChangeListener extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface $operator
     */
    function let($operator)
    {
        $this->beConstructedWith($operator);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\EventListener\InventoryChangeListener');
    }

    function it_should_be_a_Sylius_inventory_change_listener()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\EventListener\InventoryChangeListenerInterface');
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent         $event
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_fill_backorders_on_inventory_change($operator, $event, $stockable)
    {
        $event->getSubject()->shouldBeCalled()->willReturn($stockable);
        $operator->fillBackorders($stockable)->shouldBeCalled();

        $this->onInventoryChange($event);
    }
}
