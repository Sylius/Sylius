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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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

    function it_creates_inventory_units(
            InventoryHandlerInterface $inventoryHandler,
            GenericEvent $event,
            OrderItemInterface $item
    )
    {
        $event->getSubject()->willReturn($item);

        $inventoryHandler->processInventoryUnits($item)->shouldBeCalled();

        $this->processInventoryUnits($event);
    }
}
