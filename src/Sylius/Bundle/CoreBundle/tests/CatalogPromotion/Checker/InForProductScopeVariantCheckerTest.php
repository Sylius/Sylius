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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class InForProductScopeVariantCheckerTest extends TestCase
{
    private InForProductScopeVariantChecker $inForProductScopeVariantChecker;

    protected function setUp(): void
    {
        $this->inForProductScopeVariantChecker = new InForProductScopeVariantChecker();
    }

    public function testImplementsCatalogPromotionPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(VariantInScopeCheckerInterface::class, $this->inForProductScopeVariantChecker);
    }

    public function testReturnsTrueIfProductVariantIsInScopeConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);

        $scope->method('getConfiguration')->willReturn(['products' => ['FIRST_PRODUCT', 'SECOND_PRODUCT']]);
        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('getCode')->willReturn('FIRST_PRODUCT');

        $this->assertTrue($this->inForProductScopeVariantChecker->inScope($scope, $variant));
    }

    public function testReturnsFalseIfProductVariantIsNotInScopeConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);

        $scope->method('getConfiguration')->willReturn(['products' => ['FIRST_PRODUCT', 'SECOND_PRODUCT']]);
        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('getCode')->willReturn('ANOTHER_PRODUCT');

        $this->assertFalse($this->inForProductScopeVariantChecker->inScope($scope, $variant));
    }

    public function testThrowsExceptionIfScopeDoesNotContainsProductConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $scope->expects($this->once())->method('getConfiguration')->willReturn(['FOO' => ['BOO']]);
        $this->expectException(InvalidArgumentException::class);

        $this->inForProductScopeVariantChecker->inScope($scope, $variant);
    }
}
