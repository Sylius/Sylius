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
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\IncrementPromotionUsagesCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class IncrementPromotionUsagesCallbackSpec extends ObjectBehavior
{
    function let(OrderPromotionsUsageModifierInterface $promotionsUsageModifier): void
    {
        $this->beConstructedWith($promotionsUsageModifier);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(IncrementPromotionUsagesCallback::class);
    }

    function it_is_called_after_placed_order(): void
    {
        $this->shouldImplement(AfterPlacedOrderCallbackInterface::class);
    }

    function it_increments_promotion_usages(
        OrderInterface $order,
        OrderPromotionsUsageModifierInterface $promotionsUsageModifier,
    ): void
    {
        $promotionsUsageModifier->increment($order)->shouldBeCalled();

        $this->call($order);
    }
}
