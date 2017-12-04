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

interface PaymentInterface extends TimestampableInterface, ResourceInterface
{
    public const STATE_AUTHORIZED = 'authorized';
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
    public function getMethod(): ?PaymentMethodInterface;

    /**
     * @param PaymentMethodInterface|null $method
     */
    public function setMethod(?PaymentMethodInterface $method): void;

    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @param string $state
     */
    public function setState(string $state): void;

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string;

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode): void;

    /**
     * @return int|null
     */
    public function getAmount(): ?int;

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void;

    /**
     * @return array
     */
    public function getDetails(): array;

    /**
     * @param array $details
     */
    public function setDetails(array $details): void;
}
