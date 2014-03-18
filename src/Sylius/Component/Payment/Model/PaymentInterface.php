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

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Single payment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PaymentInterface extends TimestampableInterface
{
    // Payment states.
    const STATE_NEW        = 'new';
    const STATE_PENDING    = 'pending';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETED  = 'completed';
    const STATE_FAILED     = 'failed';
    const STATE_CANCELLED  = 'cancelled';
    const STATE_VOID       = 'void';
    const STATE_REFUNDED   = 'refunded';
    const STATE_UNKNOWN    = 'unknown';

    /**
     * Get payment method associated with this payment.
     *
     * @return PaymentMethodInterface
     */
    public function getMethod();

    /**
     * Set payment method.
     *
     * @param null|PaymentMethodInterface $method
     *
     * @return PaymentInterface
     */
    public function setMethod(PaymentMethodInterface $method = null);

    /**
     * Get payment source.
     *
     * @return PaymentSourceInterface
     */
    public function getSource();

    /**
     * Set payment source.
     *
     * @param null|PaymentSourceInterface $source
     *
     * @return PaymentInterface
     */
    public function setSource(PaymentSourceInterface $source = null);

    /**
     * Get state.
     *
     * @return string
     */
    public function getState();

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return PaymentInterface
     */
    public function setState($state);

    /**
     * Get payment currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency.
     *
     * @param string
     *
     * @return PaymentInterface
     */
    public function setCurrency($currency);

    /**
     * Get amount.
     *
     * @return integer
     */
    public function getAmount();

    /**
     * Set amount.
     *
     * @param integer $amount
     *
     * @return PaymentInterface
     */
    public function setAmount($amount);

    /**
     * @param array $details
     *
     * @return PaymentInterface
     */
    public function setDetails(array $details);

    /**
     * @return array
     */
    public function getDetails();
}
