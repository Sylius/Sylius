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
 * Standard pricing calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StandardCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = [])
    {
        return $subject->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::STANDARD;
    }
}
