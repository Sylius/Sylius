<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PaymentInterface extends TimestampableInterface, ResourceInterface
{
    public const STATE_CART = 'cart';
    public const STATE_NEW = 'new';
    public const STATE_PROCESSING = 'processing';
    public const STATE_COMPLETED = 'completed';
    public const STATE_FAILED = 'failed';
    public const STATE_CANCELLED = 'cancelled';
    public const STATE_REFUNDED = 'refunded';
    public const STATE_UNKNOWN = 'unknown';

    /**
     * @return PaymentMethodInterface
     */
    public function getMethod();

    /**
     * @param null|PaymentMethodInterface $method
     */
    public function setMethod(PaymentMethodInterface $method = null);

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
    public function getCurrencyCode();

    /**
     * @param string
     */
    public function setCurrencyCode($currencyCode);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @return array
     */
    public function getDetails();

    /**
     * @param array|\Traversable $details
     */
    public function setDetails($details);
}
