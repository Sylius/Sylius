<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Order\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPromotionsIntegrityCheckerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor): void
    {
        $this->beConstructedWith($orderProcessor);
    }

    function it_returns_null_if_promotion_is_valid(
        OrderInterface $order,
        PromotionInterface $promotion,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $order->getPromotions()->willReturn(
            new ArrayCollection([$promotion->getWrappedObject()]),
        );

        $orderProcessor->process($order)->shouldBeCalled();

        $this->check($order)->shouldReturn(null);
    }

    function it_returns_promotion_if_promotion_is_not_valid(
        OrderInterface $order,
        PromotionInterface $oldPromotion,
        PromotionInterface $newPromotion,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $order->getPromotions()->willReturn(
            new ArrayCollection([$oldPromotion->getWrappedObject()]),
            new ArrayCollection([$newPromotion->getWrappedObject()]),
        );

        $orderProcessor->process($order)->shouldBeCalled();

        $this->check($order)->shouldReturn($oldPromotion);
    }
}
