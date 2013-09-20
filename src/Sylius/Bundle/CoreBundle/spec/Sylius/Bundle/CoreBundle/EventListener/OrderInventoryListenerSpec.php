<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderInventoryListenerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\OrderProcessing\InventoryHandlerInterface $inventoryHandler
     */
    function let($inventoryHandler)
    {
        $this->beConstructedWith($inventoryHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderInventoryListener');
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param \stdClass                                      $invalidSubject
     */
    function it_throws_exception_if_event_has_non_order_subject($event, $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringOnOrderPreComplete($event)
        ;
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface  $order
     */
    function it_updates_inventory_on_order_pre_complete_event($inventoryHandler, $event, $order)
    {
        $event->getSubject()->willReturn($order);
        $inventoryHandler->updateInventory($order)->shouldBeCalled();

        $this->onOrderPreComplete($event);
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface  $order
     */
    function it_processes_inventory_units_on_cart_change_event($inventoryHandler, $event, $order)
    {
        $event->getSubject()->willReturn($order);
        $inventoryHandler->processInventoryUnits($order)->shouldBeCalled();

        $this->onCartChange($event);
    }
}
