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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class IncrementPromotionUsagesListenerSpec extends ObjectBehavior
{
    function let(OrderPromotionsUsageModifierInterface $orderPromotionsUsageModifier): void
    {
        $this->beConstructedWith($orderPromotionsUsageModifier);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $callback): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($callback->getWrappedObject(), new Marking())]);
    }

    function it_increments_promotion_usages(
        OrderPromotionsUsageModifierInterface $orderPromotionsUsageModifier,
        OrderInterface $order,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());

        $this($event);

        $orderPromotionsUsageModifier->increment($order)->shouldBeCalled();
    }
}
