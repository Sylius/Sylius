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

use Sylius\Component\Resource\Model\ImageInterface;

interface ProductVariantImageInterface extends ImageInterface
{
    /**
     * Get product variant.
     *
     * @return ProductVariantInterface
     */
    public function getVariant();

    /**
     * Set the product variant.
     *
     * @param null|ProductVariantInterface $variant
     *
     * @return self
     */
    public function setVariant(ProductVariantInterface $variant = null);
}
