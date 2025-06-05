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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantCatalogPromotionsProcessorTest extends TestCase
{
    private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface&MockObject $commandDispatcher;

    private ProductVariantCatalogPromotionsProcessor $productVariantCatalogPromotionsProcessor;

    protected function setUp(): void
    {
        $this->commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $this->productVariantCatalogPromotionsProcessor = new ProductVariantCatalogPromotionsProcessor($this->commandDispatcher);
    }

    public function testImplementsProductCatalogPromotionsProcessorInterface(): void
    {
        $this->assertInstanceOf(ProductVariantCatalogPromotionsProcessorInterface::class, $this->productVariantCatalogPromotionsProcessor);
    }

    public function testReappliesCatalogPromotionOnVariant(): void
    {
        $variantMock = $this->createMock(ProductVariantInterface::class);

        $variantMock->expects($this->once())->method('getCode')->willReturn('VARIANT_CODE');

        $this->commandDispatcher->updateVariants(['VARIANT_CODE']);

        $this->productVariantCatalogPromotionsProcessor->process($variantMock);
    }
}
