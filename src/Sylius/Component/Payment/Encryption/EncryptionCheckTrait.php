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

namespace Sylius\Component\Payment\Encryption;

/** @experimental */
trait EncryptionCheckTrait
{
    protected function isEncrypted(mixed $value): bool
    {
        return is_string($value) && str_ends_with($value, EncrypterInterface::ENCRYPTION_SUFFIX);
    }
}
