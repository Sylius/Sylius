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
use Sylius\Component\Payment\Resolver\CompositeMethodsResolver;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

final class CompositeMethodsResolverTest extends TestCase
{
    private MockObject $resolversRegistry;

    private CompositeMethodsResolver $compositeMethodsResolver;

    private MockObject $firstMethodsResolver;

    private MockObject $secondMethodsResolver;

    /** @var PaymentInterface&MockObject */
    private MockObject $payment;

    protected function setUp(): void
    {
        $this->resolversRegistry = $this->createMock(PrioritizedServiceRegistryInterface::class);
        $this->compositeMethodsResolver = new CompositeMethodsResolver($this->resolversRegistry);
        $this->firstMethodsResolver = $this->createMock(PaymentMethodsResolverInterface::class);
        $this->secondMethodsResolver = $this->createMock(PaymentMethodsResolverInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
    }

    public function testImplementsSyliusPaymentMethodsResolverInterface(): void
    {
        $this->assertInstanceOf(PaymentMethodsResolverInterface::class, $this->compositeMethodsResolver);
    }

    public function testUsesRegistryToProvidePaymentMethodsForPayment(): void
    {
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);

        $this->resolversRegistry
            ->expects($this->once())
            ->method('all')
            ->willReturn([$this->firstMethodsResolver, $this->secondMethodsResolver]);
        $this->firstMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)
            ->willReturn(false);
        $this->secondMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)
            ->willReturn(true);
        $this->secondMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->payment)
            ->willReturn([$paymentMethodMock]);

        $this->assertSame(
            [$paymentMethodMock],
            $this->compositeMethodsResolver->getSupportedMethods($this->payment),
        );
    }

    public function testReturnsEmptyArrayIfNoneOfRegisteredResolversSupportPassedPayment(): void
    {
        $this->resolversRegistry
            ->expects($this->once())
            ->method('all')
            ->willReturn([$this->firstMethodsResolver, $this->secondMethodsResolver]);
        $this->firstMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)
            ->willReturn(false);
        $this->secondMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)
            ->willReturn(false);

        $this->assertSame(
            [],
            $this->compositeMethodsResolver->getSupportedMethods($this->payment),
        );
    }

    public function testSupportsPaymentIfAtLeastOneRegisteredResolverSupportsIt(): void
    {
        $this->resolversRegistry
            ->expects($this->once())
            ->method('all')
            ->willReturn([$this->firstMethodsResolver, $this->secondMethodsResolver]);
        $this->firstMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)->willReturn(false);
        $this->secondMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)->willReturn(true);

        $this->assertTrue($this->compositeMethodsResolver->supports($this->payment));
    }

    public function testDoesNotSupportPaymentIfNoneOfRegisteredResolversSupportsIt(): void
    {
        $this->resolversRegistry
            ->expects($this->once())
            ->method('all')
            ->willReturn([$this->firstMethodsResolver, $this->secondMethodsResolver]);
        $this->firstMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)
            ->willReturn(false);
        $this->secondMethodsResolver
            ->expects($this->once())
            ->method('supports')
            ->with($this->payment)
            ->willReturn(false);

        $this->assertFalse($this->compositeMethodsResolver->supports($this->payment));
    }
}
