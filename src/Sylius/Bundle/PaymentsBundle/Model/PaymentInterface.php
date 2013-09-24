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
 * Single payment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PaymentInterface extends TimestampableInterface
{
    // Payment states.
    const STATE_CHECKOUT   = 'checkout';
    const STATE_PROCESSING = 'processing';
    const STATE_PENDING    = 'pending';
    const STATE_FAILED     = 'failed';
    const STATE_VOID       = 'void';
    const STATE_COMPLETED  = 'completed';

    /**
     * Get payment method associated with this payment.
     *
     * @return PaymentMethodInterface
     */
    public function getMethod();

    /**
     * Set payment method.
     *
     * @param PaymentMethodInterface $method
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
     */
    public function setAmount($amount);

    /**
     * Get processing logs.
     *
     * @return Collection
     */
    public function getLogs();

    /**
     * Has given log already?
     *
     * @param PaymentLogInterface $log
     *
     * @return Boolean
     */
    public function hasLog(PaymentLogInterface $log);

    /**
     * Add payment processing log.
     *
     * @param PaymentLogInterface $log
     */
    public function addLog(PaymentLogInterface $log);

    /**
     * Remove payment processing log.
     *
     * @param PaymentLogInterface $log
     */
    public function removeLog(PaymentLogInterface $log);
}
