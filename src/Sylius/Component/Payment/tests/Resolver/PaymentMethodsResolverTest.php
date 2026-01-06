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
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolver;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class PaymentMethodsResolverTest extends TestCase
{
    private MockObject $methodRepository;

    private PaymentMethodsResolver $paymentMethodsResolver;

    /** @var PaymentInterface&MockObject */
    private MockObject $payment;

    protected function setUp(): void
    {
        $this->methodRepository = $this->createMock(RepositoryInterface::class);
        $this->paymentMethodsResolver = new PaymentMethodsResolver($this->methodRepository);
        $this->payment = $this->createMock(PaymentInterface::class);
    }

    public function testImplementsMethodsResolverInterface(): void
    {
        $this->assertInstanceOf(PaymentMethodsResolverInterface::class, $this->paymentMethodsResolver);
    }

    public function testReturnsAllMethodsEnabledForGivenPayment(): void
    {
        $method1Mock = $this->createMock(PaymentMethodInterface::class);
        $method2Mock = $this->createMock(PaymentMethodInterface::class);

        $this->methodRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn([$method1Mock, $method2Mock]);

        $this->assertSame(
            [$method1Mock, $method2Mock],
            $this->paymentMethodsResolver->getSupportedMethods($this->payment),
        );
    }

    public function testSupportsEveryPayment(): void
    {
        $this->assertTrue($this->paymentMethodsResolver->supports($this->payment));
    }
}
