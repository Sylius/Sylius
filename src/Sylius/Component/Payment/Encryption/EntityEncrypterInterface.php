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

/**
 * @template T of EncryptionAwareInterface
 *
 * @experimental
 */
interface EntityEncrypterInterface
{
    /**
     * @phpstan-param T $resource
     */
    public function encrypt(EncryptionAwareInterface $resource): void;

    /**
     * @phpstan-param T $resource
     */
    public function decrypt(EncryptionAwareInterface $resource): void;
}
