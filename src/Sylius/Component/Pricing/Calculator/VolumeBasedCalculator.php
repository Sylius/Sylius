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
        $quantity = array_key_exists('quantity', $context) ? $context['quantity'] : 1;

        foreach ($configuration as $range) {
            if (empty($range['max']) && $quantity > $range['min']) {
                return $range['price'];
            }

            if ($range['min'] <= $quantity && $quantity <= $range['max']) {
                return $range['price'];
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::VOLUME_BASED;
    }
}
