<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Listener;

use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;

final class PaymentRequestEncryptionListener extends EntityEncryptionListener
{
    /** @param class-string $paymentRequestClass  */
    public function __construct(
        EntityEncrypterInterface $entityEncrypter,
        private readonly string $paymentRequestClass,
    ) {
        parent::__construct($entityEncrypter);
    }

    protected function supports(mixed $entity): bool
    {
        return is_a($entity, $this->paymentRequestClass, true);
    }
}
