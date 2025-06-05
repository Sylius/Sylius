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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\DecrementPromotionUsagesListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class DecrementPromotionUsagesListenerTest extends TestCase
{
    private MockObject&OrderPromotionsUsageModifierInterface $orderPromotionsUsageModifier;

    private DecrementPromotionUsagesListener $listener;

    protected function setUp(): void
    {
        $this->orderPromotionsUsageModifier = $this->createMock(OrderPromotionsUsageModifierInterface::class);
        $this->listener = new DecrementPromotionUsagesListener($this->orderPromotionsUsageModifier);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDecrementsPromotionUsages(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderPromotionsUsageModifier
            ->expects($this->once())
            ->method('decrement')
            ->with($order)
        ;

        ($this->listener)($event);
    }
}
