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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Checker\Eligibility\CategoryRequirementEligibilityChecker;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class CategoryRequirementEligibilityCheckerTest extends TestCase
{
    private MockObject&ShippingSubjectInterface $shippingSubject;

    private MockObject&ShippingMethodInterface $shippingMethod;

    private MockObject&ShippingCategoryInterface $shippingCategory;

    private MockObject&ShippingCategoryInterface $shippingCategory2;

    private MockObject&ShippableInterface $shippable;

    private CategoryRequirementEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->shippingSubject = $this->createMock(ShippingSubjectInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shippingCategory = $this->createMock(ShippingCategoryInterface::class);
        $this->shippingCategory2 = $this->createMock(ShippingCategoryInterface::class);
        $this->shippingCategory2 = $this->createMock(ShippingCategoryInterface::class);
        $this->shippable = $this->createMock(ShippableInterface::class);
        $this->checker = new CategoryRequirementEligibilityChecker();
    }

    public function testShouldImplementShippingMethodEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldApproveCategoryRequirementIfCategoriesMatch(): void
    {
        $this->shippingMethod->expects($this->exactly(3))->method('getCategory')->willReturn($this->shippingCategory);
        $this->shippingMethod->expects($this->exactly(3))->method('getCategoryRequirement')->willReturn(
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY,
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL,
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE,
        );
        $this->shippable->expects($this->exactly(3))->method('getShippingCategory')->willReturn(
            $this->shippingCategory,
            $this->shippingCategory,
            $this->shippingCategory2,
        );

        $this->shippingSubject->expects($this->exactly(3))->method('getShippables')->willReturn(
            new ArrayCollection([$this->shippable]),
        );

        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldApproveCategoryRequirementIfNoCategoryIsRequired(): void
    {
        $this->shippingMethod->expects($this->once())->method('getCategory')->willReturn(null);

        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldDenyCategoryRequirementIfCategoriesDoNotMatch(): void
    {
        $this->shippingMethod->expects($this->exactly(3))->method('getCategory')->willReturn(
            $this->shippingCategory,
            $this->shippingCategory,
            $this->shippingCategory2,
        );
        $this->shippingMethod->expects($this->exactly(3))->method('getCategoryRequirement')->willReturn(
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY,
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL,
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE,
        );
        $this->shippable->expects($this->exactly(3))->method('getShippingCategory')->willReturn($this->shippingCategory2);
        $this->shippingSubject->expects($this->exactly(3))->method('getShippables')->willReturn(
            new ArrayCollection([$this->shippable]),
        );

        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }
}
