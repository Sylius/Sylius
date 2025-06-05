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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibility;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ProductVariantForCatalogPromotionEligibilityTest extends TestCase
{
    private MockObject&ServiceLocator $locator;

    private ProductVariantForCatalogPromotionEligibility $productVariantForCatalogPromotionEligibility;

    protected function setUp(): void
    {
        $this->locator = $this->createMock(ServiceLocator::class);
        $this->productVariantForCatalogPromotionEligibility = new ProductVariantForCatalogPromotionEligibility($this->locator);
    }

    public function testImplementsCatalogPromotionPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(ProductVariantForCatalogPromotionEligibilityInterface::class, $this->productVariantForCatalogPromotionEligibility);
    }

    public function testReturnTrueIfVariantFitsForAnyCatalogPromotionScopeConfiguration(): void
    {
        $promotion = $this->createMock(CatalogPromotionInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $forVariantsScope = $this->createMock(CatalogPromotionScopeInterface::class);
        $forVariantsChecker = $this->createMock(VariantInScopeCheckerInterface::class);

        $promotion
            ->expects($this->once())
            ->method('getScopes')
            ->willReturn(new ArrayCollection([$forVariantsScope]))
        ;

        $forVariantsScope
            ->expects($this->once())
            ->method('getType')
            ->willReturn(InForVariantsScopeVariantChecker::TYPE)
        ;

        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with(InForVariantsScopeVariantChecker::TYPE)
            ->willReturn($forVariantsChecker)
        ;

        $forVariantsChecker
            ->expects($this->once())
            ->method('inScope')
            ->with($forVariantsScope, $variant)
            ->willReturn(true)
        ;

        $this->assertTrue($this->productVariantForCatalogPromotionEligibility->isApplicableOnVariant($promotion, $variant));
    }

    public function testReturnFalseIfVariantDoesNotFitForAnyCatalogPromotionScopeConfiguration(): void
    {
        $promotion = $this->createMock(CatalogPromotionInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $forTaxonsScope = $this->createMock(CatalogPromotionScopeInterface::class);
        $forTaxonsChecker = $this->createMock(VariantInScopeCheckerInterface::class);

        $promotion
            ->expects($this->once())
            ->method('getScopes')
            ->willReturn(new ArrayCollection([$forTaxonsScope]))
        ;

        $forTaxonsScope
            ->expects($this->once())
            ->method('getType')
            ->willReturn(InForTaxonsScopeVariantChecker::TYPE)
        ;

        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with(InForTaxonsScopeVariantChecker::TYPE)
            ->willReturn($forTaxonsChecker)
        ;

        $forTaxonsChecker
            ->expects($this->once())
            ->method('inScope')
            ->with($forTaxonsScope, $variant)
            ->willReturn(false)
        ;

        $this->assertFalse($this->productVariantForCatalogPromotionEligibility->isApplicableOnVariant($promotion, $variant));
    }
}
