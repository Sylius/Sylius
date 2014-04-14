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
 * Volume based pricing calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VolumeBasedCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array())
    {
        if (array_key_exists('quantity', $context)) {
            $quantity = $context['quantity'];
        } else {
            $quantity = 1;
        }

        foreach ($configuration as $range) {
            if (empty($range['max']) && $quantity > $range['min'])  {
                $price = $range['price']; break;
            }

            if ($range['min'] <= $quantity && $quantity <= $range['max']) {
                $price = $range['price']; break;
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return DefaultCalculators::VOLUME_BASED;
    }
}
