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

namespace Tests\Sylius\Component\Shipping\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\CompositeMethodsResolver;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class CompositeMethodsResolverTest extends TestCase
{
    private MockObject&PrioritizedServiceRegistryInterface $prioritizedServiceRegistry;

    private MockObject&ShippingMethodsResolverInterface $firstMethodsResolver;

    private MockObject&ShippingMethodsResolverInterface $secondMethodsResolver;

    private MockObject&ShippingMethodInterface $shippingMethod;

    private MockObject&ShippingSubjectInterface $shippingSubject;

    private CompositeMethodsResolver $resolver;

    protected function setUp(): void
    {
        $this->prioritizedServiceRegistry = $this->createMock(PrioritizedServiceRegistryInterface::class);
        $this->firstMethodsResolver = $this->createMock(ShippingMethodsResolverInterface::class);
        $this->secondMethodsResolver = $this->createMock(ShippingMethodsResolverInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shippingSubject = $this->createMock(ShippingSubjectInterface::class);
        $this->resolver = new CompositeMethodsResolver($this->prioritizedServiceRegistry);
    }

    public function testShouldImplementMethodsResolverInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodsResolverInterface::class, $this->resolver);
    }

    public function testShouldUseRegistryToProvideShippingMethodsForShippingSubject(): void
    {
        $this->prioritizedServiceRegistry->expects($this->once())->method('all')->willReturn([
            $this->firstMethodsResolver,
            $this->secondMethodsResolver,
        ]);
        $this->firstMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(false);
        $this->secondMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(true);
        $this->secondMethodsResolver->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->shippingSubject)
            ->willReturn([$this->shippingMethod]);

        $this->assertSame([$this->shippingMethod], $this->resolver->getSupportedMethods($this->shippingSubject));
    }

    public function testShouldReturnEmptyArrayIfNoneOfRegisteredResolversSupportPassedShippingSubject(): void
    {
        $this->prioritizedServiceRegistry->expects($this->once())->method('all')->willReturn([
            $this->firstMethodsResolver,
            $this->secondMethodsResolver,
        ]);
        $this->firstMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(false);
        $this->secondMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(false);

        $this->assertSame([], $this->resolver->getSupportedMethods($this->shippingSubject));
    }

    public function testShouldSupportSubjectIfAnyResolverFromRegistrySupportsIt(): void
    {
        $this->prioritizedServiceRegistry->expects($this->once())->method('all')->willReturn([
            $this->firstMethodsResolver,
            $this->secondMethodsResolver,
        ]);
        $this->firstMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(false);
        $this->secondMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(true);

        $this->assertTrue($this->resolver->supports($this->shippingSubject));
    }

    public function testShouldNotSupportSubjectIfNoneOfResolversFromRegistrySupportsIt(): void
    {
        $this->prioritizedServiceRegistry->expects($this->once())->method('all')->willReturn([
            $this->firstMethodsResolver,
            $this->secondMethodsResolver,
        ]);
        $this->firstMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(false);
        $this->secondMethodsResolver->expects($this->once())->method('supports')->with($this->shippingSubject)->willReturn(false);

        $this->assertFalse($this->resolver->supports($this->shippingSubject));
    }
}
