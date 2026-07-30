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

namespace Tests\Sylius\Bundle\ApiBundle\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Checker\OrderPaymentRequestEligibilityChecker;
use Sylius\Bundle\ApiBundle\Checker\OrderPaymentRequestEligibilityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;

final class OrderPaymentRequestEligibilityCheckerTest extends TestCase
{
    private OrderPaymentRequestEligibilityChecker $orderPaymentRequestEligibilityChecker;

    private MockObject&OrderInterface $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderPaymentRequestEligibilityChecker = new OrderPaymentRequestEligibilityChecker();
        $this->order = $this->createMock(OrderInterface::class);
    }

    public function testImplementsOrderPaymentRequestEligibilityCheckerInterface(): void
    {
        self::assertInstanceOf(
            OrderPaymentRequestEligibilityCheckerInterface::class,
            $this->orderPaymentRequestEligibilityChecker,
        );
    }

    public function testReturnsTrueIfOrderCheckoutStateIsCompleted(): void
    {
        $this->order->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_COMPLETED);

        self::assertTrue($this->orderPaymentRequestEligibilityChecker->isEligible($this->order));
    }

    public function testReturnsFalseIfOrderCheckoutStateIsNotCompleted(): void
    {
        $this->order->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_CART);

        self::assertFalse($this->orderPaymentRequestEligibilityChecker->isEligible($this->order));
    }
}
