<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Listener;

use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;

final class GatewayConfigEncryptionListener extends EntityEncryptionListener
{
    /** @param class-string $entityClass  */
    public function __construct(
        EntityEncrypterInterface $entityEncrypter,
        string $entityClass,
        private readonly array $disabledGateways,
    ) {
        parent::__construct($entityEncrypter, $entityClass);
    }

    protected function supports(mixed $entity): bool
    {
        return
            parent::supports($entity) &&
            !in_array($entity->getFactoryName(), $this->disabledGateways, true)
        ;
    }
}
