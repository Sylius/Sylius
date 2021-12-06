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

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

final class OrderPromotionProcessorSpec extends ObjectBehavior
{
    function let(PromotionProcessorInterface $promotionProcessor): void
    {
        $this->beConstructedWith($promotionProcessor);
    }

    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_processes_promotions(PromotionProcessorInterface $promotionProcessor, OrderInterface $order): void
    {
        $order->getState()->willReturn(OrderInterface::STATE_CART);

        $promotionProcessor->process($order)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_is_in_a_state_different_than_cart(
        PromotionProcessorInterface $promotionProcessor,
        OrderInterface $order
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $promotionProcessor->process($order)->shouldNotBeCalled();

        $this->process($order);
    }
}
