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
use Webmozart\Assert\Assert;

/**
 * @implements EntityEncrypterInterface<GatewayConfigInterface>
 *
 * @experimental
 */
final readonly class GatewayConfigEncrypter implements EntityEncrypterInterface
{
    use EncryptionCheckTrait;

    /** @param list<class-string> $allowedClasses */
    public function __construct(
        private EncrypterInterface $encrypter,
        private bool|array $allowedClasses = true,
    ) {
        if (true === $this->allowedClasses) {
            trigger_deprecation(
                'sylius/payment',
                '2.1',
                'Passing "true" as allowed classes is deprecated and support for it will be removed in Sylius 3.0. Please provide an explicit list of allowed classes or set it to "false" if you do not want to allow any classes.',
            );
        }
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
        if (!$this->allIsEncrypted($config)) {
            return;
        }

        $decryptedConfig = [];
        foreach ($config as $key => $value) {
            $decryptedConfig[$key] = unserialize($this->encrypter->decrypt($value), ['allowed_classes' => $this->allowedClasses]);
        }

        if ([] !== $decryptedConfig) {
            $resource->setConfig($decryptedConfig);
        }
    }
}
