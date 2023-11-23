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

interface PaymentRequestInterface extends TimestampableInterface, ResourceInterface
{
    public const STATE_NEW = 'new';

    public const STATE_PROCESSING = 'processing';

    public const STATE_FAILED = 'failed';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_COMPLETED = 'completed';

    public const DATA_TYPE_CAPTURE = 'capture';

    public const DATA_TYPE_AUTHORIZE = 'authorize';

    public const DATA_TYPE_STATUS = 'status';

    public const DATA_TYPE_SYNC = 'sync';

    public const DATA_TYPE_PAYOUT = 'payout';

    public function getHash(): ?string;

    public function getMethod(): ?PaymentMethodInterface;

    public function setMethod(?PaymentMethodInterface $method): void;

    public function getPayment(): ?PaymentInterface;

    public function setPayment(?PaymentInterface $payment): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getData(): mixed;

    public function setData(mixed $data): void;

    /**
     * @return mixed[]
     */
    public function getDetails(): array;

    /**
     * @param mixed[] $details
     */
    public function setDetails(array $details): void;
}
