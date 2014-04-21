<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Model;

interface PaymentSubjectInterface
{
    /**
     * Get the payment associated with the payment subject.
     *
     * @return PaymentInterface
     */
    public function getPayment();

    /**
     * Set payment.
     *
     * @param PaymentInterface $payment
     */
    public function setPayment(PaymentInterface $payment);
} 
