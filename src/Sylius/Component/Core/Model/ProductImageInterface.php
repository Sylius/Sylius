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

use Doctrine\Common\Collections\Collection;

/**
 * @author Saidul Islam <saidul.04@gmail.com>
 */
interface ProductImageInterface extends ImageInterface
{
    /**
     * @return bool
     */
    public function hasProductVariants();

    /**
     * @return Collection|ProductVariantInterface[]
     */
    public function getProductVariants();

    /**
     * @param ProductVariantInterface $productVariant
     */
    public function addProductVariant(ProductVariantInterface $productVariant);

    /**
     * @param ProductVariantInterface $productVariant
     */
    public function removeProductVariant(ProductVariantInterface $productVariant);

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return bool
     */
    public function hasProductVariant(ProductVariantInterface $productVariant);
}
