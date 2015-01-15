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

use Doctrine\Common\Collections\Collection;

interface PaymentsSubjectInterface
{
    /**
     * Get all payments associated with the payment subject.
     *
     * @return Collection|PaymentInterface[]
     */
    public function getPayments();

    /**
     * Check if order has any payments
     *
     * @return bool
     */
    public function hasPayments();

    /**
     * Add a payment.
     *
     * @param PaymentInterface $payment
     */
    public function addPayment(PaymentInterface $payment);

    /**
     * Remove a payment.
     *
     * @param PaymentInterface $payment
     */
    public function removePayment(PaymentInterface $payment);

    /**
     * Check if the payment subject has certain payment.
     *
     * @param PaymentInterface $payment
     *
     * @return bool
     */
    public function hasPayment(PaymentInterface $payment);
}
