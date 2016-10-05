<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Modifier;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifier;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 *
 * @mixin OrderPromotionsUsageModifier
 */
final class OrderPromotionsUsageModifierSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPromotionsUsageModifier::class);
    }

    function it_implements_order_promotions_usage_modifier_interface()
    {
        $this->shouldImplement(OrderPromotionsUsageModifierInterface::class);
    }

    function it_increment_usage_of_promotions_applied_on_order(
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);

        $firstPromotion->incrementUsed()->shouldBeCalled();
        $secondPromotion->incrementUsed()->shouldBeCalled();

        $this->increment($order);
    }

    function it_decrements_usage_of_promotions_applied_on_order(
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);

        $firstPromotion->decrementUsed()->shouldBeCalled();
        $secondPromotion->decrementUsed()->shouldBeCalled();

        $this->decrement($order);
    }
}
