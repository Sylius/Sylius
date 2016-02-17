<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\StateMachineCallback;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

class PromotionUsageCallbackSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\PromotionUsageCallback');
    }

    function it_increments_promotion_usage_if_promotion_was_used(
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->getPromotions()->willReturn([$promotion]);

        $promotion->incrementUsed()->shouldBeCalled();

        $this->incrementPromotionUsage($order);
    }

    function it_decrements_promotion_usage_if_promotion_was_used(
        OrderInterface $order,
        PromotionInterface $promotion
    ) {
        $order->getPromotions()->willReturn([$promotion]);

        $promotion->getUsed()->willReturn(5);
        $promotion->setUsed(4)->shouldBeCalled();

        $this->decrementPromotionUsage($order);
    }
}
