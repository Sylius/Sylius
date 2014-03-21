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

interface VariantImageInterface extends ImageInterface
{
    /**
     * Get variant.
     *
     * @return VariantInterface
     */
    public function getVariant();

    /**
     * Set the variant.
     *
     * @param VariantInterface $variant
     */
    public function setVariant(VariantInterface $variant = null);
}
