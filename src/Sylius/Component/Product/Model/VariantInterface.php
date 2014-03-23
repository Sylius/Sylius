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

use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * Product variant interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariantInterface extends BaseVariantInterface
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
     * @param |null $product
     */
    public function setProduct(ProductInterface $product = null);

    /**
     * Check whether the product is available.
     */
    public function isAvailable();

    /**
     * Return available on.
     *
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * Set available on.
     *
     * @param \DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn);
}
