<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Calculator;

use Sylius\Component\Payment\Model\PaymentSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
interface DelegatingFeeCalculatorInterface
{
    /**
     * @param PaymentSubjectInterface $payment
     *
     * @return int
     */
    public function calculate(PaymentSubjectInterface $payment);
}
