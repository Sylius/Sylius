<?php

declare(strict_types=1);

namespace Sylius\Component\Payment\Encryption;

/**
 * @template T of EncryptionAwareInterface
 */
interface EntityEncrypterInterface
{
    /**
     * @param EncryptionAwareInterface $resource
     * @phpstan-param T $resource
     */
    public function encrypt(EncryptionAwareInterface $resource): void;

    /**
     * @param EncryptionAwareInterface $resource
     * @phpstan-param T $resource
     */
    public function decrypt(EncryptionAwareInterface $resource): void;
}
