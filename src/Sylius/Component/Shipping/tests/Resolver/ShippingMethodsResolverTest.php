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
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolver;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class ShippingMethodsResolverTest extends TestCase
{
    /** @var ShippingMethodRepositoryInterface<ShippingMethodInterface>&MockObject */
    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private MockObject&ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private ShippingMethodsResolver $resolver;

    protected function setUp(): void
    {
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->eligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->resolver = new ShippingMethodsResolver($this->shippingMethodRepository, $this->eligibilityChecker);
    }

    public function testShouldImplementSyliusShippingMethodsResolverInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodsResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnAllMethodsEligibleForGivenSubject(): void
    {
        $shippingSubject = $this->createMock(ShippingSubjectInterface::class);
        $method1 = $this->createMock(ShippingMethodInterface::class);
        $method2 = $this->createMock(ShippingMethodInterface::class);
        $method3 = $this->createMock(ShippingMethodInterface::class);

        $this->shippingMethodRepository->expects($this->once())->method('findEnabledWithRules')->willReturn([
            $method1,
            $method2,
            $method3,
        ]);
        $checkerInvokedCount = $this->exactly(3);
        $this->eligibilityChecker->expects($checkerInvokedCount)->method('isEligible')->willReturnCallback(
            function ($params) use ($checkerInvokedCount): bool {
                if ($checkerInvokedCount->numberOfInvocations() === 3) {
                    return false;
                }

                return true;
            },
        );

        $this->assertSame([$method1, $method2], $this->resolver->getSupportedMethods($shippingSubject));
    }
}
