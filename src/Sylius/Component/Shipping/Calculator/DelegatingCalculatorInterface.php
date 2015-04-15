<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * Delegating calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DelegatingCalculatorInterface
{
    /**
     * Calculate the shipping charge for given subject.
     *
     * @param ShippingSubjectInterface $subject
     *
     * @return int
     */
    public function calculate(ShippingSubjectInterface $subject);
}
