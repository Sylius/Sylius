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

namespace Sylius\Component\Payment\Exception;

/** @experimental */
final class InvalidPaymentRequestPayloadException extends \RuntimeException
{
    public function __construct(
        string $message = 'Payload of the payment request is invalid.',
        int $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
