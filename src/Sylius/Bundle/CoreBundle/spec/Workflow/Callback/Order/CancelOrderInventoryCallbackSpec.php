<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelOrderInventoryCallback;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CancelOrderInventoryCallbackSpec extends ObjectBehavior
{
    function let(OrderInventoryOperatorInterface $orderInventoryOperator): void
    {
        $this->beConstructedWith($orderInventoryOperator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CancelOrderInventoryCallback::class);
    }

    function it_cancels_order_inventory(
        OrderInterface $order,
        OrderInventoryOperatorInterface $orderInventoryOperator,
    ): void {
        $orderInventoryOperator->cancel($order)->shouldBeCalled();

        $this->call($order);
    }
}
