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
use Sylius\Component\Core\Inventory\Handler\PaidOrderInventoryHandler;
use Sylius\Component\Core\Inventory\Handler\PaidOrderInventoryHandlerInterface;
use Sylius\Component\Core\Inventory\Updater\DecreasingQuantityUpdaterInterface;
use Sylius\Component\Core\Inventory\Updater\IncreasingQuantityUpdaterInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @mixin PaidOrderInventoryHandler
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaidOrderInventoryHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PaidOrderInventoryHandler::class);
    }

    function it_implements_paid_order_inventory_handler()
    {
        $this->shouldImplement(PaidOrderInventoryHandlerInterface::class);
    }

    function it_handles_inventory_for_paid_order(
        DecreasingQuantityUpdaterInterface $onHandQuantityUpdater,
        DecreasingQuantityUpdaterInterface $onHoldQuantityUpdater,
        OrderInterface $order
    ) {
        $this->addDecreasingQuantityUpdater($onHandQuantityUpdater);
        $this->addDecreasingQuantityUpdater($onHoldQuantityUpdater);

        $onHandQuantityUpdater->decrease($order)->shouldBeCalled();
        $onHoldQuantityUpdater->decrease($order)->shouldBeCalled();

        $this->handle($order);
    }

    function it_throws_handle_exception_if_decreasing_quantity_updater_fails(
        DecreasingQuantityUpdaterInterface $onHandQuantityUpdater,
        OrderInterface $order
    ) {
        $this->addDecreasingQuantityUpdater($onHandQuantityUpdater);
        $onHandQuantityUpdater->decrease($order)->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(HandleException::class)->during('handle', [$order]);
    }

    function it_throws_handle_exception_if_there_is_no_nested_decreasing_updaters(OrderInterface $order)
    {
        $this->shouldThrow(HandleException::class)->during('handle', [$order]);
    }
}
