<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment\AfterPaidCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment\OrderPaidCallback;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderPaidCallbackSpec extends ObjectBehavior
{
    function let(OrderInventoryOperatorInterface $inventoryOperator): void
    {
        $this->beConstructedWith($inventoryOperator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderPaidCallback::class);
    }

    function it_is_called_after_paid(): void
    {
        $this->shouldImplement(AfterPaidCallbackInterface::class);
    }

    function it_sets_the_order_as_paid(
        OrderInterface $order,
        OrderInventoryOperatorInterface $inventoryOperator,
    ): void {
        $inventoryOperator->sell($order)->shouldBeCalled();

        $this->call($order);
    }
}
