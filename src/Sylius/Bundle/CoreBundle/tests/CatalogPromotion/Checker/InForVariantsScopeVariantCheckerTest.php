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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class InForVariantsScopeVariantCheckerTest extends TestCase
{
    private InForVariantsScopeVariantChecker $inForVariantsScopeVariantChecker;

    protected function setUp(): void
    {
        $this->inForVariantsScopeVariantChecker = new InForVariantsScopeVariantChecker();
    }

    public function testImplementsCatalogPromotionPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(VariantInScopeCheckerInterface::class, $this->inForVariantsScopeVariantChecker);
    }

    public function testReturnsTrueIfVariantIsInScopeConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $scope
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(['variants' => ['FIRST_VARIANT', 'SECOND_VARIANT']])
        ;

        $variant->expects($this->once())->method('getCode')->willReturn('SECOND_VARIANT');

        $this->assertTrue($this->inForVariantsScopeVariantChecker->inScope($scope, $variant));
    }

    public function testReturnsFalseIfVariantIsNotInScopeConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $scope
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(['variants' => ['FIRST_VARIANT', 'SECOND_VARIANT']])
        ;

        $variant->expects($this->once())->method('getCode')->willReturn('THIRD_VARIANTS');

        $this->assertFalse($this->inForVariantsScopeVariantChecker->inScope($scope, $variant));
    }

    public function testThrowsExceptionIfScopeDoesNotContainsProductConfiguration(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $scope->expects($this->once())->method('getConfiguration')->willReturn(['FOO' => ['BOO']]);
        $this->expectException(InvalidArgumentException::class);

        $this->inForVariantsScopeVariantChecker->inScope($scope, $variant);
    }
}
