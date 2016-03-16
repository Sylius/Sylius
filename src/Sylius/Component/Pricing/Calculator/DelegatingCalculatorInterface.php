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
 * Delegating calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DelegatingCalculatorInterface
{
    /**
     * Calculate the price for given subject.
     *
     * @param PriceableInterface $subject
     * @param array              $context
     *
     * @return int
     */
    public function calculate(PriceableInterface $subject, array $context = []);
}
