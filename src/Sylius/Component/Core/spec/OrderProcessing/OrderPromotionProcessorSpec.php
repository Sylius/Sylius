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
        $order->canBeProcessed()->willReturn(true);

        $promotionProcessor->process($order)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_cannot_be_processed(
        PromotionProcessorInterface $promotionProcessor,
        OrderInterface $order,
    ): void {
        $order->canBeProcessed()->willReturn(false);

        $promotionProcessor->process($order)->shouldNotBeCalled();

        $this->process($order);
    }
}
