<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Payment processing log entry interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PaymentLogInterface extends TimestampableInterface
{
    /**
     * Get payment.
     *
     * @return PaymentInterface
     */
    public function getPayment();

    /**
     * Set payment method.
     *
     * @param PaymentInterface $payment
     */
    public function setPayment(PaymentInterface $payment = null);

    /**
     * Get message.
     *
     * @return integer
     */
    public function getMessage();

    /**
     * Set message.
     *
     * @param integer $message
     */
    public function setMessage($message);
}
