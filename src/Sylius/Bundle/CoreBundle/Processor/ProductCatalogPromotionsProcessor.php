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

use Sylius\Bundle\CoreBundle\Announcer\BatchedVariantsUpdateAnnouncerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;


final class ProductCatalogPromotionsProcessor implements ProductCatalogPromotionsProcessorInterface
{
    public function __construct(
        private CatalogPromotionClearerInterface $catalogPromotionClearer,
        private BatchedVariantsUpdateAnnouncerInterface $announcer
    ) {
    }

    public function process(ProductInterface $product): void
    {
        $variants = $product->getVariants()->toArray();
        $this->clearVariants($variants);

        $variantsCodes = array_map(
            fn (ProductVariantInterface $variant): string => $variant->getCode(),
            $variants
        );

        $this->announcer->dispatchVariantsUpdateCommand($variantsCodes);
    }

    private function clearVariants(array $variants): void
    {
        foreach ($variants as $variant) {
            $this->catalogPromotionClearer->clearVariant($variant);
        }
    }
}
