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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PaymentInterface extends TimestampableInterface, ResourceInterface
{
    // Payment states.
    const STATE_NEW = 'new';
    const STATE_PENDING = 'pending';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETED = 'completed';
    const STATE_AUTHORIZED = 'authorized';
    const STATE_FAILED = 'failed';
    const STATE_CANCELLED = 'cancelled';
    const STATE_VOID = 'void';
    const STATE_REFUNDED = 'refunded';
    const STATE_UNKNOWN = 'unknown';
    const STATE_PAYEDOUT = 'payedout';

    /**
     * @return PaymentMethodInterface
     */
    public function getMethod();

    /**
     * @param null|PaymentMethodInterface $method
     */
    public function setMethod(PaymentMethodInterface $method = null);

    /**
     * @return PaymentSourceInterface
     */
    public function getSource();

    /**
     * @param null|PaymentSourceInterface $source
     */
    public function setSource(PaymentSourceInterface $source = null);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string
     */
    public function setCurrency($currency);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @param array|\Traversable $details
     */
    public function setDetails($details);

    /**
     * @return array
     */
    public function getDetails();
}
