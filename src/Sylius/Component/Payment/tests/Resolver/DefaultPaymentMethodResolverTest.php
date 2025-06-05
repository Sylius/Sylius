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

namespace Tests\Sylius\Component\Payment\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolver;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

final class DefaultPaymentMethodResolverTest extends TestCase
{
    private MockObject $paymentMethodRepository;

    private DefaultPaymentMethodResolver $defaultPaymentMethodResolver;

    /** @var PaymentInterface&MockObject */
    private MockObject $payment;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->defaultPaymentMethodResolver = new DefaultPaymentMethodResolver($this->paymentMethodRepository);
        $this->payment = $this->createMock(PaymentInterface::class);
    }

    public function testImplementsDefaultPaymentMethodResolverInterface(): void
    {
        $this->assertInstanceOf(DefaultPaymentMethodResolverInterface::class, $this->defaultPaymentMethodResolver);
    }

    public function testReturnsFirstEnabledPaymentMethodAsDefault(): void
    {
        $firstPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $secondPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);

        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn([$firstPaymentMethodMock, $secondPaymentMethodMock]);

        $this->assertSame(
            $firstPaymentMethodMock,
            $this->defaultPaymentMethodResolver->getDefaultPaymentMethod($this->payment),
        );
    }

    public function testThrowsExceptionIfThereAreNoEnabledPaymentMethods(): void
    {
        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn([]);

        $this->expectException(UnresolvedDefaultPaymentMethodException::class);

        $this->defaultPaymentMethodResolver->getDefaultPaymentMethod($this->payment);
    }
}
