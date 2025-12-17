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

use Sylius\Component\Payment\Encryption\Exception\EncryptionException;

/** @experimental */
interface EncrypterInterface
{
    public const ENCRYPTION_SUFFIX = '#ENCRYPTED';

    public const ENCRYPTION_SUFFIX_LENGTH = 10;

    /** @throws EncryptionException */
    public function encrypt(string $data): string;

    /** @throws EncryptionException */
    public function decrypt(string $data): string;
}
