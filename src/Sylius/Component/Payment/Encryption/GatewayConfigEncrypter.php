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

use Sylius\Component\Payment\Model\GatewayConfigInterface;

/**
 * @implements EntityEncrypterInterface<GatewayConfigInterface>
 *
 * @experimental
 */
final readonly class GatewayConfigEncrypter implements EntityEncrypterInterface
{
    use EncryptionCheckTrait;

    public function __construct(
        private EncrypterInterface $encrypter,
    ) {
    }

    public function encrypt(EncryptionAwareInterface $resource): void
    {
        $encryptedConfig = [];
        foreach ($resource->getConfig() as $key => $value) {
            $encryptedConfig[$key] = $this->encrypter->encrypt(serialize($value));
        }

        $resource->setConfig($encryptedConfig);
    }

    public function decrypt(EncryptionAwareInterface $resource): void
    {
        if (!$this->isEncrypted(current($resource->getConfig()))) {
            return;
        }

        $decryptedConfig = [];
        foreach ($resource->getConfig() as $key => $value) {
            $decryptedConfig[$key] = unserialize($this->encrypter->decrypt($value));
        }

        $resource->setConfig($decryptedConfig);
    }
}
