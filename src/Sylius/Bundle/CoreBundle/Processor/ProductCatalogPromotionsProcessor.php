<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Bundle\CoreBundle\Commander\UpdateVariantsCommanderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;


final class ProductCatalogPromotionsProcessor implements ProductCatalogPromotionsProcessorInterface
{
    public function __construct(
        private UpdateVariantsCommanderInterface $commander
    ) {
    }

    public function process(ProductInterface $product): void
    {
        $variants = $product->getVariants()->toArray();

        $variantsCodes = array_map(
            fn (ProductVariantInterface $variant): string => $variant->getCode(),
            $variants
        );

        $this->commander->updateVariants($variantsCodes);
    }
}
