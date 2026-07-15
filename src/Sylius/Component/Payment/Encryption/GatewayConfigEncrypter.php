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
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Webmozart\Assert\Assert;

/**
 * @implements EntityEncrypterInterface<GatewayConfigInterface>
 *
 * @experimental
 */
final readonly class GatewayConfigEncrypter implements EntityEncrypterInterface
{
    use EncryptionCheckTrait;

    /** @param bool|list<class-string> $allowedClasses */
    public function __construct(
        private EncrypterInterface $encrypter,
        private array|bool $allowedClasses = true,
        private bool $strictMode = false,
    ) {
        if (is_array($this->allowedClasses)) {
            Assert::allStringNotEmpty($allowedClasses, 'Each allowed class must be a non-empty string. Got: %s');
            Assert::allClassExists($allowedClasses, 'Allowed class %s does not exist.');
        }
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
        $config = $resource->getConfig();
        if ([] === $config) {
            return;
        }

        if ($this->strictMode) {
            if (!$this->isFullyEncrypted($config)) {
                throw EncryptionException::dataIsNotFullyEncrypted();
            }
        } elseif (!$this->isEncrypted(current($config))) {
            return;
        }

        $decryptedConfig = [];
        foreach ($config as $key => $value) {
            $decryptedConfig[$key] = unserialize($this->encrypter->decrypt($value), ['allowed_classes' => $this->allowedClasses]);
        }

        $resource->setConfig($decryptedConfig);
    }

    /** @param array<array-key, mixed> $values */
    private function isFullyEncrypted(array $values): bool
    {
        foreach ($values as $value) {
            if (!$this->isEncrypted($value)) {
                return false;
            }
        }

        return true;
    }
}
