<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Inventory\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Inventory\Handler\OrderInventoryHandler;
use Sylius\Component\Core\Inventory\Updater\OrderQuantityUpdaterInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @mixin OrderInventoryHandler
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderInventoryHandlerSpec extends ObjectBehavior
{
    function let(
        OrderQuantityUpdaterInterface $onHandQuantityUpdater,
        OrderQuantityUpdaterInterface $onHoldQuantityUpdater
    ) {
        $this->beConstructedWith($onHandQuantityUpdater, $onHoldQuantityUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderInventoryHandler::class);
    }

    function it_implements_paid_order_inventory_handler()
    {
        $this->shouldImplement(OrderInventoryHandler::class);
    }

    function it_handles_inventory_for_paid_order(
        OrderQuantityUpdaterInterface $onHandQuantityUpdater,
        OrderQuantityUpdaterInterface $onHoldQuantityUpdater,
        OrderInterface $order
    ) {
        $onHandQuantityUpdater->decrease($order)->shouldBeCalled();
        $onHoldQuantityUpdater->decrease($order)->shouldBeCalled();

        $this->handle($order);
    }

    function it_throws_handle_exception_if_decreasing_quantity_updater_fails(
        OrderQuantityUpdaterInterface $onHandQuantityUpdater,
        OrderInterface $order
    ) {
        $onHandQuantityUpdater->decrease($order)->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(HandleException::class)->during('handle', [$order]);
    }
}
