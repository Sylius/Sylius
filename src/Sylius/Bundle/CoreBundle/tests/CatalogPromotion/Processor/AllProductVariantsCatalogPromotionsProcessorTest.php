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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessor;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AllProductVariantsCatalogPromotionsProcessorTest extends TestCase
{
    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface&MockObject $commandDispatcher;

    private AllProductVariantsCatalogPromotionsProcessor $processor;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $this->processor = new AllProductVariantsCatalogPromotionsProcessor(
            $this->productVariantRepository,
            $this->commandDispatcher,
        );
    }

    public function testClearsAndProcessesCatalogPromotions(): void
    {
        $this->productVariantRepository
            ->method('getCodesOfAllVariants')
            ->willReturn(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'])
        ;

        $this->commandDispatcher
            ->expects($this->once())
            ->method('updateVariants')
            ->with(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'])
        ;

        $this->processor->process();
    }
}
