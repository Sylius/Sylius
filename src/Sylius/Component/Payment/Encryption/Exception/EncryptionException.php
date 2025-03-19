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

namespace Sylius\Component\Payment\Encryption\Exception;

/** @experimental */
final class EncryptionException extends \RuntimeException
{
    public static function cannotEncrypt(\Throwable $previousException): self
    {
        return new self(
            message: 'Cannot encrypt data.',
            previous: $previousException,
        );
    }

    public static function cannotDecrypt(\Throwable $previousException): self
    {
        return new self(
            message: 'Cannot decrypt data.',
            previous: $previousException,
        );
    }

    public static function invalidKey(\Throwable $previousException): self
    {
        return new self(
            message: 'Invalid encryption key.',
            previous: $previousException,
        );
    }
}
