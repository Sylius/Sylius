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

namespace Tests\Sylius\Component\Shipping\Checker\Eligibility;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Checker\Eligibility\CompositeShippingMethodEligibilityChecker;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class CompositeShippingMethodEligibilityCheckerTest extends TestCase
{
    private MockObject&ShippingMethodEligibilityCheckerInterface $firstShippingMethodEligibilityChecker;

    private MockObject&ShippingMethodEligibilityCheckerInterface $secondShippingMethodEligibilityChecker;

    private MockObject&ShippingSubjectInterface $shippingSubject;

    private MockObject&ShippingMethodInterface $shippingMethod;

    private CompositeShippingMethodEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->firstShippingMethodEligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->secondShippingMethodEligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->shippingSubject = $this->createMock(ShippingSubjectInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->checker = new CompositeShippingMethodEligibilityChecker([
            $this->firstShippingMethodEligibilityChecker,
            $this->secondShippingMethodEligibilityChecker,
        ]);
    }

    public function testShouldImplementShippingMethodEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldThrowAnExceptionIfPassedArrayHasNotOnlyEligibilityCheckers(): void
    {
        /** @var ShippingMethodEligibilityCheckerInterface[] $eligibilityCheckers */
        $eligibilityCheckers = [new \stdClass()];

        $this->expectException(\InvalidArgumentException::class);

        new CompositeShippingMethodEligibilityChecker($eligibilityCheckers);
    }

    public function testShouldReturnTrueIfAllEligibilityCheckerReturnTrue(): void
    {
        $this->firstShippingMethodEligibilityChecker->expects($this->once())->method('isEligible')->willReturn(true);
        $this->secondShippingMethodEligibilityChecker->expects($this->once())->method('isEligible')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldReturnFalseIfAnyEligibilityCheckerReturnFalse(): void
    {
        $this->firstShippingMethodEligibilityChecker->expects($this->once())->method('isEligible')->willReturn(true);
        $this->secondShippingMethodEligibilityChecker->expects($this->once())->method('isEligible')->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldStopCheckingAtTheFirstFailingEligibilityChecker(): void
    {
        $this->firstShippingMethodEligibilityChecker->expects($this->once())->method('isEligible')->willReturn(false);
        $this->secondShippingMethodEligibilityChecker->expects($this->never())->method('isEligible');

        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }
}
