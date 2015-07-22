<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Pricing\Model;

/**
 * Priceable interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PriceableInterface
{
    /**
     * Get standard price.
     *
     * @return int
     */
    public function getPrice();

    /**
     * Set standard price.
     *
     * @param int $price
     */
    public function setPrice($price);

    /**
     * Get the name of pricing calculator.
     *
     * @return string
     */
    public function getPricingCalculator();

    /**
     * Set the pricing calculation service type.
     *
     * @param string $calculator
     */
    public function setPricingCalculator($calculator);

    /**
     * Get pricing configuration.
     *
     * @return array
     */
    public function getPricingConfiguration();

    /**
     * Set pricing configuration.
     *
     * @param array $configuration
     */
    public function setPricingConfiguration(array $configuration);
}
