<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Pricing\Calculator;

use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * Price calculator interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CalculatorInterface
{
    /**
     * Calculate price for the priceable object with given configuration and context.
     *
     * @param PriceableInterface $subject
     * @param array              $configuration
     * @param array              $context
     *
     * @return int
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = []);

    /**
     * Get calculator type.
     *
     * @return string
     */
    public function getType();
}
