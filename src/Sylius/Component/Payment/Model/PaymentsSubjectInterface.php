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
     * @return Collection|PaymentInterface[]
     */
    public function getPayments();

    /**
     * @return bool
     */
    public function hasPayments();

    /**
     * @param PaymentInterface $payment
     */
    public function addPayment(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     */
    public function removePayment(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return bool
     */
    public function hasPayment(PaymentInterface $payment);
}
