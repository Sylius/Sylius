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

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryChangeListenerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\InventoryBundle\Operator\BackordersHandlerInterface $backordersHandler
     */
    function let($backordersHandler)
    {
        $this->beConstructedWith($backordersHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\EventListener\InventoryChangeListener');
    }

    function it_implements_Sylius_inventory_change_listener_interface()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\EventListener\InventoryChangeListenerInterface');
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent         $event
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_fills_backorders_on_inventory_change($backordersHandler, $event, $stockable)
    {
        $event->getSubject()->shouldBeCalled()->willReturn($stockable);
        $backordersHandler->fillBackorders($stockable)->shouldBeCalled();

        $this->onInventoryChange($event);
    }
}
