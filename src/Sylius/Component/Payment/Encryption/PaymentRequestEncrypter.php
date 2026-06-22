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
use Webmozart\Assert\Assert;

/**
 * @implements EntityEncrypterInterface<PaymentRequestInterface>
 *
 * @experimental
 */
final readonly class PaymentRequestEncrypter implements EntityEncrypterInterface
{
    use EncryptionCheckTrait;

    /** @param bool|list<class-string> $allowedClasses */
    public function __construct(
        private EncrypterInterface $encrypter,
        private array|bool $allowedClasses = true,
    ) {
        if (is_array($this->allowedClasses)) {
            Assert::allStringNotEmpty($allowedClasses, 'Each allowed class must be a non-empty string. Got: %s');
            Assert::allClassExists($allowedClasses, 'Allowed class %s does not exist.');
        }
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
            $resource->setPayload(unserialize($this->encrypter->decrypt($resource->getPayload()), ['allowed_classes' => $this->allowedClasses]));
        }

        $responseData = $resource->getResponseData();
        if (!$this->allIsEncrypted($responseData)) {
            return;
        }

        $decryptedRequestData = [];
        foreach ($responseData as $key => $value) {
            $decryptedRequestData[$key] = unserialize($this->encrypter->decrypt($value), ['allowed_classes' => $this->allowedClasses]);
        }

        if ([] !== $decryptedRequestData) {
            $resource->setResponseData($decryptedRequestData);
        }
    }
}
