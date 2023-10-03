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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

final class ProductCatalogPromotionsProcessor implements ProductCatalogPromotionsProcessorInterface
{
    public function __construct(
        private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
    ) {
    }

    public function process(ProductInterface $product): void
    {
        $variants = $product->getVariants()->toArray();

        $variantsCodes = array_map(
            fn (ProductVariantInterface $variant): string => $variant->getCode(),
            $variants,
        );

        $this->commandDispatcher->updateVariants($variantsCodes);
    }
}
