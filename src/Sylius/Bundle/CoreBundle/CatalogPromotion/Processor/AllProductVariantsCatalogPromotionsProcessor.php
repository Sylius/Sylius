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
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
        private int $batchSize,
    ) {
    }

    public function process(): void
    {
        // The catalog is read batch by batch and each batch is dispatched as soon as it is read,
        // so peak memory stays bounded by the batch size instead of growing with the catalog.
        foreach ($this->productVariantRepository->iterateCodesOfAllVariants($this->batchSize) as $variantsCodes) {
            $this->commandDispatcher->updateVariants($variantsCodes);
        }
    }
}
