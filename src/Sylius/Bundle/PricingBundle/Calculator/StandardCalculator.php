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
 * Standard pricing calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StandardCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array())
    {
        return $subject->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return false;
    }
}

