<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface VariantInterface extends BaseVariantInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param null|ProductInterface $product
     */
    public function setProduct(ProductInterface $product = null);

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * @param null|\DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn = null);

    /**
     * @return \DateTime
     */
    public function getAvailableUntil();

    /**
     * @param null|\DateTime $availableUntil
     */
    public function setAvailableUntil(\DateTime $availableUntil = null);
}
