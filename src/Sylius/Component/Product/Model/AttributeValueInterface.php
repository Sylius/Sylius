<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\AttributeValueInterface as BaseAttributeValueInterface;

/**
 * Product to attribute relation interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface AttributeValueInterface extends BaseAttributeValueInterface
{
    /**
     * Get product.
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set product.
     *
     * @param ProductInterface|null $product
     */
    public function setProduct(ProductInterface $product = null);
}
