<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Calculator;

use Sylius\Bundle\PricingBundle\Model\PriceableInterface;

/**
 * Price calculator interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
     * @return integer
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array());

    /**
     * Get calculator configuration form type, if any required.
     *
     * @return string|Boolean Returns false if configuration is not required
     */
    public function getConfigurationFormType();
}
