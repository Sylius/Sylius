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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AllProductVariantsCatalogPromotionsProcessor implements AllProductVariantsCatalogPromotionsProcessorInterface
{
    public const DEFAULT_BATCH_SIZE = 100;

    private int $batchSize;

    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
        ?int $batchSize = null,
    ) {
        if (null === $batchSize) {
            trigger_deprecation(
                'sylius/core-bundle',
                '2.3',
                'Not passing a batch size as the third constructor argument of "%s" is deprecated and will be prohibited in Sylius 3.0.',
                self::class,
            );
        }

        $this->batchSize = $batchSize ?? self::DEFAULT_BATCH_SIZE;
    }

    public function process(): void
    {
        // Each batch is dispatched as soon as it is read, so peak memory stays bounded by the
        // batch size instead of growing with the catalog.
        foreach ($this->productVariantRepository->iterateCodesOfAllVariants($this->batchSize) as $variantsCodes) {
            $this->commandDispatcher->updateVariants($variantsCodes);
        }
    }
}
