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
    private const BATCH_SIZE = 2;

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
            self::BATCH_SIZE,
        );
    }

    public function testDispatchesEveryBatchOfVariantCodes(): void
    {
        $this->productVariantRepository
            ->expects($this->once())
            ->method('iterateCodesOfAllVariants')
            ->with(self::BATCH_SIZE)
            ->willReturn([
                ['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'],
                ['THIRD_VARIANT_CODE'],
            ])
        ;

        $dispatchedBatches = [];
        $this->commandDispatcher
            ->expects($this->exactly(2))
            ->method('updateVariants')
            ->willReturnCallback(function (array $variantsCodes) use (&$dispatchedBatches): void {
                $dispatchedBatches[] = $variantsCodes;
            })
        ;

        $this->processor->process();

        $this->assertSame(
            [['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'], ['THIRD_VARIANT_CODE']],
            $dispatchedBatches,
        );
    }

    public function testDispatchesNothingWhenThereAreNoVariants(): void
    {
        $this->productVariantRepository
            ->expects($this->once())
            ->method('iterateCodesOfAllVariants')
            ->with(self::BATCH_SIZE)
            ->willReturn([])
        ;

        $this->commandDispatcher->expects($this->never())->method('updateVariants');

        $this->processor->process();
    }

    public function testConsumesBatchesLazilyWithoutMaterialisingTheWholeCatalog(): void
    {
        $readBatches = 0;

        $this->productVariantRepository
            ->expects($this->once())
            ->method('iterateCodesOfAllVariants')
            ->willReturnCallback(function () use (&$readBatches): \Generator {
                foreach ([['FIRST_VARIANT_CODE'], ['SECOND_VARIANT_CODE']] as $batch) {
                    ++$readBatches;

                    yield $batch;
                }
            })
        ;

        $dispatchedWhileReading = [];
        $this->commandDispatcher
            ->expects($this->exactly(2))
            ->method('updateVariants')
            ->willReturnCallback(function () use (&$readBatches, &$dispatchedWhileReading): void {
                // Each dispatch must happen while only the batches read so far have been pulled
                // from the repository, never after the whole catalog has been loaded.
                $dispatchedWhileReading[] = $readBatches;
            })
        ;

        $this->processor->process();

        $this->assertSame([1, 2], $dispatchedWhileReading);
    }

    public function testFallsBackToTheDefaultBatchSizeWhenNoneIsGiven(): void
    {
        // Pinned to a literal because the deprecated no-argument path only preserves the previous
        // behaviour while this constant equals the default of sylius_core.catalog_promotions.batch_size.
        $this->assertSame(100, AllProductVariantsCatalogPromotionsProcessor::DEFAULT_BATCH_SIZE);

        $processor = new AllProductVariantsCatalogPromotionsProcessor(
            $this->productVariantRepository,
            $this->commandDispatcher,
        );

        $this->productVariantRepository
            ->expects($this->once())
            ->method('iterateCodesOfAllVariants')
            ->with(AllProductVariantsCatalogPromotionsProcessor::DEFAULT_BATCH_SIZE)
            ->willReturn([])
        ;

        $this->commandDispatcher->expects($this->never())->method('updateVariants');

        $processor->process();
    }
}
