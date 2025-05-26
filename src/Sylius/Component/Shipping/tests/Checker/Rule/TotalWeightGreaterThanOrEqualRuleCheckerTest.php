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

namespace Tests\Sylius\Component\Shipping\Checker\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class TotalWeightGreaterThanOrEqualRuleCheckerTest extends TestCase
{
    private MockObject&ShippingSubjectInterface $shippingSubject;

    private TotalWeightGreaterThanOrEqualRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->shippingSubject = $this->createMock(ShippingSubjectInterface::class);
        $this->ruleChecker = new TotalWeightGreaterThanOrEqualRuleChecker();
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRecognizeSubjectIfTotalWeightIsGreaterThanConfiguredWeight(): void
    {
        $this->shippingSubject->expects($this->once())->method('getShippingWeight')->willReturn(6.0);

        $this->assertTrue($this->ruleChecker->isEligible($this->shippingSubject, ['weight' => 5]));
    }

    public function testShouldRecognizeSubjectIfTotalWeightIsEqualToConfiguredWeight(): void
    {
        $this->shippingSubject->expects($this->once())->method('getShippingWeight')->willReturn(5.0);

        $this->assertTrue($this->ruleChecker->isEligible($this->shippingSubject, ['weight' => 5]));
    }

    public function testShouldDenySubjectIfTotalWeightIsLessThanConfiguredWeight(): void
    {
        $this->shippingSubject->expects($this->once())->method('getShippingWeight')->willReturn(4.0);

        $this->assertFalse($this->ruleChecker->isEligible($this->shippingSubject, ['weight' => 5]));
    }
}
