<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\EventListener\InventoryChangeListenerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryChangeListenerSpec extends ObjectBehavior
{
    function let(BackordersHandlerInterface $backordersHandler)
    {
        $this->beConstructedWith($backordersHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\EventListener\InventoryChangeListener');
    }

    function it_implements_Sylius_inventory_change_listener_interface()
    {
        $this->shouldImplement(InventoryChangeListenerInterface::class);
    }

    function it_fills_backorders_on_inventory_change(
        $backordersHandler,
        GenericEvent $event,
        StockableInterface $stockable)
    {
        $event->getSubject()->shouldBeCalled()->willReturn($stockable);
        $backordersHandler->fillBackorders($stockable)->shouldBeCalled();

        $this->onInventoryChange($event);
    }
}
