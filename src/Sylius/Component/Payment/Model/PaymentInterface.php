<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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

    public function getMethod(): ?PaymentMethodInterface;

    public function setMethod(?PaymentMethodInterface $method): void;

    public function getState(): ?string;

    public function setState(string $state): void;

    public function getCurrencyCode(): ?string;

    public function setCurrencyCode(string $currencyCode): void;

    public function getAmount(): ?int;

    public function setAmount(int $amount): void;

    public function getDetails(): array;

    public function setDetails(array $details): void;
}
