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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\BlockSelectPaymentFromPaymentSkippedListener;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\Transition;

final class BlockSelectPaymentFromPaymentSkippedListenerTest extends TestCase
{
    private MockObject&OrderPaymentMethodSelectionAvailabilityCheckerInterface $availabilityChecker;

    private BlockSelectPaymentFromPaymentSkippedListener $listener;

    protected function setUp(): void
    {
        $this->availabilityChecker = $this->createMock(OrderPaymentMethodSelectionAvailabilityCheckerInterface::class);
        $this->listener = new BlockSelectPaymentFromPaymentSkippedListener($this->availabilityChecker);
    }

    public function testItDoesNothingWhenMarkingIsNotPaymentSkipped(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = $this->createGuardEvent($order, ['shipping_selected' => 1]);

        $this->availabilityChecker->expects($this->never())->method('isPaymentMethodSelectionAvailable');

        ($this->listener)($event);

        $this->assertFalse($event->isBlocked());
    }

    public function testItBlocksTransitionWhenSelectingPaymentIsNotAvailable(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = $this->createGuardEvent($order, [OrderCheckoutStates::STATE_PAYMENT_SKIPPED => 1]);

        $this->availabilityChecker
            ->expects($this->once())
            ->method('isPaymentMethodSelectionAvailable')
            ->with($order)
            ->willReturn(false)
        ;

        ($this->listener)($event);

        $this->assertTrue($event->isBlocked());
    }

    public function testItDoesNotBlockTransitionWhenSelectingPaymentIsAvailable(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = $this->createGuardEvent($order, [OrderCheckoutStates::STATE_PAYMENT_SKIPPED => 1]);

        $this->availabilityChecker
            ->expects($this->once())
            ->method('isPaymentMethodSelectionAvailable')
            ->with($order)
            ->willReturn(true)
        ;

        ($this->listener)($event);

        $this->assertFalse($event->isBlocked());
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $event = $this->createGuardEvent(new \stdClass(), [OrderCheckoutStates::STATE_PAYMENT_SKIPPED => 1]);

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    private function createGuardEvent(object $subject, array $places): GuardEvent
    {
        return new GuardEvent(
            $subject,
            new Marking($places),
            new Transition('select_payment', OrderCheckoutStates::STATE_PAYMENT_SKIPPED, 'payment_selected'),
        );
    }
}
