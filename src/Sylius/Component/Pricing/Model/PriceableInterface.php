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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PriceableInterface
{
    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param int $price
     */
    public function setPrice($price);

    /**
     * @return string
     */
    public function getPricingCalculator();

    /**
     * @param string $calculator
     */
    public function setPricingCalculator($calculator);

    /**
     * @return array
     */
    public function getPricingConfiguration();

    /**
     * @param array $configuration
     */
    public function setPricingConfiguration(array $configuration);
}
