<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface as BaseVariantInterface;

interface VariantImageInterface extends ImageInterface
{
    /**
     * Get variant.
     *
     * @return \Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface
     */
    public function getVariant();

    /**
     * Set the variant.
     *
     * @param \Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $variant
     */
    public function setVariant(BaseVariantInterface $variant = null);
}
