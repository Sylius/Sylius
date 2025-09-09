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

namespace Sylius\Tests\Checker\Eligibility;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Checker\OrderPaymentMethodEligibilityChecker;

final class OrderPaymentMethodEligibilityCheckerTest extends TestCase
{
    private OrderPaymentMethodEligibilityChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new OrderPaymentMethodEligibilityChecker();
    }

    public function test_it_returns_false_when_payment_method_is_disabled(): void
    {
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $paymentMethod
            ->method('isEnabled')
            ->willReturn(false);

        self::assertFalse($this->checker->isEligible($paymentMethod));
    }

    public function test_it_returns_true_when_payment_method_is_enabled(): void
    {
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $paymentMethod
            ->method('isEnabled')
            ->willReturn(true);

        self::assertTrue($this->checker->isEligible($paymentMethod));
    }
}
