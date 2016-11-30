<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Resolver;

use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class DefaultProductVariantResolver implements ProductVariantResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getVariant(ProductInterface $subject)
    {
        if ($subject->getVariants()->isEmpty()) {
            return null;
        }

        return $subject->getVariants()->first();
    }
}
