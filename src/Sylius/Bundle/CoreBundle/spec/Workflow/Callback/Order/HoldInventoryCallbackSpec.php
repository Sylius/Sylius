<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterPlacedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\HoldInventoryCallback;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class HoldInventoryCallbackSpec extends ObjectBehavior
{
    function let(OrderInventoryOperatorInterface $inventoryOperator): void
    {
        $this->beConstructedWith($inventoryOperator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(HoldInventoryCallback::class);
    }

    function it_is_called_after_placed_order(): void
    {
        $this->shouldImplement(AfterPlacedOrderCallbackInterface::class);
    }

    function it_holds_inventory(
        OrderInterface $order,
        OrderInventoryOperatorInterface $inventoryOperator,
    ): void
    {
        $inventoryOperator->hold($order)->shouldBeCalled();

        $this->call($order);
    }
}
