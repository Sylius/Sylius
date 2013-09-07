<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Calculator;

use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * Delegating calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface DelegatingCalculatorInterface
{
    /**
     * Calculate the shipping charge for given subject.
     *
     * @param ShippingSubjectInterface $subject
     *
     * @return integer
     */
    public function calculate(ShippingSubjectInterface $subject);
}
