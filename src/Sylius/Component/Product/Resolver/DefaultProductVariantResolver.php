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

namespace Sylius\Component\Product\Resolver;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class DefaultProductVariantResolver implements ProductVariantResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        if ($subject->getVariants()->isEmpty()) {
            return null;
        }

        return $subject->getVariants()->first();
    }
}
