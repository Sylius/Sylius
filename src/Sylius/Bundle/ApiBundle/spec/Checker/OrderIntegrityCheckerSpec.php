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

namespace spec\Sylius\Bundle\ApiBundle\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\OrderNoLongerEligibleForPromotion;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderIntegrityCheckerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor): void
    {
        $this->beConstructedWith($orderProcessor);
    }

    function it_passes_check_when_promotion_is_still_valid(
        OrderInterface $order,
        PromotionInterface $promotion,
        OrderProcessorInterface $orderProcessor
    ): void {
        $order->getPromotions()->willReturn(
            new ArrayCollection([$promotion->getWrappedObject()]),
        );

        $orderProcessor->process($order)->shouldBeCalled();

        $this->check($order);
    }

    function it_throws_an_exception_when_promotion_already_expired(
        OrderInterface $order,
        PromotionInterface $oldPromotion,
        PromotionInterface $newPromotion,
        OrderProcessorInterface $orderProcessor
    ): void {
        $order->getPromotions()->willReturn(
            new ArrayCollection([$oldPromotion->getWrappedObject()]),
            new ArrayCollection([$newPromotion->getWrappedObject()])
        );

        $oldPromotion->getName()->willReturn('Old promotion');

        $orderProcessor->process($order)->shouldBeCalled();

        $this
            ->shouldThrow(new OrderNoLongerEligibleForPromotion('Old promotion'))
            ->during('check', [$order])
        ;
    }
}
