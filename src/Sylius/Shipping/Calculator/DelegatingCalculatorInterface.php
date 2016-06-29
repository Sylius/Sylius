<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Shipping\Calculator;

use Sylius\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DelegatingCalculatorInterface
{
    /**
     * @param ShippingSubjectInterface $subject
     *
     * @return int
     */
    public function calculate(ShippingSubjectInterface $subject);
}
