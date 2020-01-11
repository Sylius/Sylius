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

namespace Sylius\Component\Product\Checker;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

final class ProductVariantsParityChecker implements ProductVariantsParityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkParity(ProductVariantInterface $variant, ProductInterface $product): bool
    {
        foreach ($product->getVariants() as $existingVariant) {
            // This check is require, because this function has to look for any other different variant with same option values set
            if ($variant === $existingVariant || count($variant->getOptionValues()) !== count($product->getOptions())) {
                continue;
            }

            if ($this->matchOptions($variant, $existingVariant)) {
                return true;
            }
        }

        return false;
    }

    private function matchOptions(ProductVariantInterface $variant, ProductVariantInterface $existingVariant): bool
    {
        foreach ($variant->getOptionValues() as $option) {
            if (!$existingVariant->hasOptionValue($option)) {
                return false;
            }
        }

        return true;
    }
}
