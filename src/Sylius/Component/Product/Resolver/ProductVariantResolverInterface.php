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
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface ProductVariantResolverInterface
{
    /**
     * @param ProductInterface $subject
     *
     * @return ProductVariantInterface
     */
    public function getVariant(ProductInterface $subject);
}
