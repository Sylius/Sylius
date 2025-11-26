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

use Sylius\Component\Payment\Model\PaymentRequestInterface;

/**
 * @implements EntityEncrypterInterface<PaymentRequestInterface>
 *
 * @experimental
 */
final readonly class PaymentRequestEncrypter implements EntityEncrypterInterface
{
    use EncryptionCheckTrait;

    public function __construct(
        private EncrypterInterface $encrypter,
    ) {
    }

    public function encrypt(EncryptionAwareInterface $resource): void
    {
        if (null !== $resource->getPayload()) {
            $resource->setPayload($this->encrypter->encrypt(serialize($resource->getPayload())));
        }

        $encryptedRequestData = [];
        foreach ($resource->getResponseData() as $key => $value) {
            $encryptedRequestData[$key] = $this->encrypter->encrypt(serialize($value));
        }

        $resource->setResponseData($encryptedRequestData);
    }

    public function decrypt(EncryptionAwareInterface $resource): void
    {
        if (null !== $resource->getPayload() && $this->isEncrypted($resource->getPayload())) {
            $resource->setPayload(unserialize($this->encrypter->decrypt($resource->getPayload())));
        }

        if (!$this->isEncrypted(current($resource->getResponseData()))) {
            return;
        }

        $decryptedRequestData = [];
        foreach ($resource->getResponseData() as $key => $value) {
            $decryptedRequestData[$key] = unserialize($this->encrypter->decrypt($value));
        }

        $resource->setResponseData($decryptedRequestData);
    }
}
