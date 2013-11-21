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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderInventoryListenerSpec extends ObjectBehavior
{
    function let(InventoryHandlerInterface $inventoryHandler)
    {
        $this->beConstructedWith($inventoryHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderInventoryListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringOnCheckoutFinalizePreComplete($event)
        ;
    }

    function it_updates_inventory_on_checkout_finalize_pre_complete_event($inventoryHandler, GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $inventoryHandler->updateInventory($order)->shouldBeCalled();

        $this->onCheckoutFinalizePreComplete($event);
    }

    function it_processes_inventory_units_on_cart_change_event($inventoryHandler, GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $inventoryHandler->processInventoryUnits($order)->shouldBeCalled();

        $this->onCartChange($event);
    }
}
