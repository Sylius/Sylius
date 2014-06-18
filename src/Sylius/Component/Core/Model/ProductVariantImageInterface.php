<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

interface ProductVariantImageInterface extends ImageInterface
{
    /**
     * Get product variant.
     *
     * @return VariantInterface
     */
    public function getVariant();

    /**
     * Set the product variant.
     *
     * @param ProductVariantInterface $variant
     */
    public function setVariant(ProductVariantInterface $variant = null);
}
