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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionEligibilityCheckerTest extends TestCase
{
    private CriteriaInterface&MockObject $firstCriterion;

    private CriteriaInterface&MockObject $secondCriterion;

    private CatalogPromotionEligibilityChecker $catalogPromotionEligibilityChecker;

    protected function setUp(): void
    {
        $this->firstCriterion = $this->createMock(CriteriaInterface::class);
        $this->secondCriterion = $this->createMock(CriteriaInterface::class);
        $this->catalogPromotionEligibilityChecker = new CatalogPromotionEligibilityChecker(
            [$this->firstCriterion, $this->secondCriterion],
        );
    }

    public function testImplementsCatalogPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(
            CatalogPromotionEligibilityCheckerInterface::class,
            $this->catalogPromotionEligibilityChecker,
        );
    }

    public function testReturnsTrueIfCatalogPromotionEligible(): void
    {
        $promotion = $this->createMock(CatalogPromotionInterface::class);

        $this->firstCriterion
            ->expects($this->once())
            ->method('verify')
            ->with($promotion)
            ->willReturn(true)
        ;

        $this->secondCriterion
            ->expects($this->once())
            ->method('verify')
            ->with($promotion)
            ->willReturn(true)
        ;

        $this->assertTrue($this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($promotion));
    }

    public function testReturnsFalseIfCatalogPromotionNotEligible(): void
    {
        $promotion = $this->createMock(CatalogPromotionInterface::class);

        $this->firstCriterion
            ->expects($this->once())
            ->method('verify')
            ->with($promotion)
            ->willReturn(false)
        ;

        $this->secondCriterion
            ->expects($this->never())
            ->method('verify')
            ->with($promotion)
        ;

        $this->assertFalse($this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($promotion));
    }
}
