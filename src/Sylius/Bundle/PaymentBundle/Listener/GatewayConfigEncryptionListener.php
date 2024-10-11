<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Listener;

use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;

final class GatewayConfigEncryptionListener extends EntityEncryptionListener
{
    /** @param class-string $gatewayConfigClass  */
    public function __construct(
        EntityEncrypterInterface $entityEncrypter,
        private readonly array $disabledGateways,
        private readonly string $gatewayConfigClass,
    ) {
        parent::__construct($entityEncrypter);
    }

    protected function supports(mixed $entity): bool
    {
        return
            is_a($entity, $this->gatewayConfigClass, true) &&
            !in_array($entity->get(), $this->disabledGateways, true)
        ;
    }
}
