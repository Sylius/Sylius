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

use Sylius\Bundle\CartBundle\Model\CartItemInterface;

/**
 * Order item interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderItemInterface extends CartItemInterface
{
    /**
     * Get the product.
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Get variant.
     *
     * @return VariantInterface
     */
    public function getVariant();

    /**
     * Set variant.
     *
     * @param VariantInterface $variant
     */
    public function setVariant(VariantInterface $variant);
}
