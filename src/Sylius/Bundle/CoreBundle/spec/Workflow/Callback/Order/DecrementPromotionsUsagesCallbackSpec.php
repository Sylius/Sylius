<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterCanceledOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\DecrementPromotionsUsagesCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class DecrementPromotionsUsagesCallbackSpec extends ObjectBehavior
{
    function let(OrderPromotionsUsageModifierInterface $orderPromotionsUsageModifier): void
    {
        $this->beConstructedWith($orderPromotionsUsageModifier);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(DecrementPromotionsUsagesCallback::class);
    }

    function it_is_called_after_canceled_order(): void
    {
        $this->shouldImplement(AfterCanceledOrderCallbackInterface::class);
    }

    function it_decrements_promotions_usages(
        OrderInterface $order,
        OrderPromotionsUsageModifierInterface $orderPromotionsUsageModifier,
    ): void {
        $orderPromotionsUsageModifier->decrement($order)->shouldBeCalled();

        $this->call($order);
    }
}
