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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductCatalogPromotionsProcessorTest extends TestCase
{
    private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface&MockObject $commandDispatcher;

    private ProductCatalogPromotionsProcessor $productCatalogPromotionsProcessor;

    protected function setUp(): void
    {
        $this->commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $this->productCatalogPromotionsProcessor = new ProductCatalogPromotionsProcessor($this->commandDispatcher);
    }

    public function testImplementsProductCatalogPromotionsProcessorInterface(): void
    {
        $this->assertInstanceOf(
            ProductCatalogPromotionsProcessorInterface::class,
            $this->productCatalogPromotionsProcessor,
        );
    }

    public function testAppliesCatalogPromotionOnProductsVariants(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);

        $product->expects($this->once())->method('getVariants')->willReturn(new ArrayCollection([
            $firstVariant,
            $secondVariant,
        ]));
        $firstVariant->expects($this->once())->method('getCode')->willReturn('PHP_MUG');
        $secondVariant->expects($this->once())->method('getCode')->willReturn('SYMFONY_MUG');

        $this->commandDispatcher
            ->expects($this->once())
            ->method('updateVariants')
            ->with(['PHP_MUG', 'SYMFONY_MUG'])
        ;

        $this->productCatalogPromotionsProcessor->process($product);
    }
}
