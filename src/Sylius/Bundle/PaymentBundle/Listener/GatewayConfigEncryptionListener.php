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

namespace Sylius\Bundle\PaymentBundle\Listener;

use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

/**
 * @extends EntityEncryptionListener<GatewayConfigInterface>
 *
 * @experimental
 */
final class GatewayConfigEncryptionListener extends EntityEncryptionListener
{
    /**
     * @param EntityEncrypterInterface<GatewayConfigInterface> $entityEncrypter
     * @param class-string $entityClass
     * @param array<string> $disabledGatewayFactories
     */
    public function __construct(
        EntityEncrypterInterface $entityEncrypter,
        string $entityClass,
        private readonly array $disabledGatewayFactories,
    ) {
        parent::__construct($entityEncrypter, $entityClass);
    }

    protected function supports(mixed $entity): bool
    {
        return
            parent::supports($entity) &&
            !in_array($entity->getFactoryName(), $this->disabledGatewayFactories, true)
        ;
    }
}
