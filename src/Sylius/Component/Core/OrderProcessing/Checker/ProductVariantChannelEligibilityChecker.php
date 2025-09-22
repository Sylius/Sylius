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

namespace Sylius\Component\Core\OrderProcessing\Checker;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ProductVariantChannelEligibilityChecker implements ProductVariantChannelEligibilityCheckerInterface
{
    public function isEligible(ProductVariantInterface $variant, ChannelInterface $channel): bool
    {
        $product = $variant->getProduct();

        if (!$product instanceof ProductInterface) {
            return false;
        }

        if (!$product->hasChannel($channel)) {
            return false;
        }

        if (!$product->isEnabled()) {
            return false;
        }

        return true;
    }
}
