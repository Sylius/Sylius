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

use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\HaliteAlert;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;
use Sylius\Component\Payment\Encryption\Exception\EncryptionException;

/** @experimental */
final class Encrypter implements EncrypterInterface
{
    private ?EncryptionKey $key = null;

    public function __construct(
        private readonly string $encryptionKeyPath,
    ) {
    }

    public function encrypt(string $data): string
    {
        try {
            return Crypto::encrypt(new HiddenString($data), $this->getKey()) . self::ENCRYPTION_SUFFIX;
        } catch (HaliteAlert|\SodiumException|\TypeError $exception) {
            throw EncryptionException::cannotEncrypt($exception);
        }
    }

    public function decrypt(string $data): string
    {
        if (!str_ends_with($data, self::ENCRYPTION_SUFFIX)) {
            return $data;
        }

        try {
            $data = substr($data, 0, -self::ENCRYPTION_SUFFIX_LENGTH);

            return Crypto::decrypt($data, $this->getKey())->getString();
        } catch (HaliteAlert|\SodiumException|\TypeError $exception) {
            throw EncryptionException::cannotDecrypt($exception);
        }
    }

    private function getKey(): EncryptionKey
    {
        if (null === $this->key) {
            try {
                $this->key = KeyFactory::loadEncryptionKey($this->encryptionKeyPath);
            } catch (CannotPerformOperation|InvalidKey $exception) {
                throw EncryptionException::invalidKey($exception);
            }
        }

        return $this->key;
    }
}
