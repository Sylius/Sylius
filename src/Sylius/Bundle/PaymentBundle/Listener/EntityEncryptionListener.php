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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\UnitOfWork;
use Sylius\Component\Payment\Encryption\EncryptionAwareInterface;
use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of EncryptionAwareInterface
 *
 * @experimental
 */
class EntityEncryptionListener
{
    /**
     * @param EntityEncrypterInterface<T> $entityEncrypter
     * @param class-string<T> $entityClass
     */
    public function __construct(
        protected readonly EntityEncrypterInterface $entityEncrypter,
        protected readonly string $entityClass,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        Assert::isInstanceOf($entityManager, EntityManagerInterface::class);
        $unitOfWork = $entityManager->getUnitOfWork();

        $this->encryptEntities($unitOfWork->getScheduledEntityInsertions(), $entityManager, $unitOfWork);
        $this->encryptEntities($unitOfWork->getScheduledEntityUpdates(), $entityManager, $unitOfWork);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        Assert::isInstanceOf($entityManager, EntityManagerInterface::class);
        $unitOfWork = $entityManager->getUnitOfWork();

        /** @var array<T> $entitiesToBeDecrypted */
        $entitiesToBeDecrypted = $unitOfWork->getIdentityMap()[$this->entityClass] ?? [];
        if ([] !== $entitiesToBeDecrypted) {
            $this->decryptEntities($entitiesToBeDecrypted);
        }
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $this->decryptEntities([$args->getObject()]);
    }

    /** @param array<T> $entities */
    protected function encryptEntities(array $entities, EntityManagerInterface $entityManager, UnitOfWork $unitOfWork): void
    {
        foreach ($entities as $entity) {
            if (!$this->supports($entity)) {
                continue;
            }

            $this->entityEncrypter->encrypt($entity);
            $metadata = $entityManager->getClassMetadata(get_class($entity));
            $unitOfWork->recomputeSingleEntityChangeSet($metadata, $entity);
        }
    }

    /** @param array<T> $entities */
    protected function decryptEntities(array $entities): void
    {
        foreach ($entities as $entity) {
            if (!$this->supports($entity)) {
                continue;
            }

            $this->entityEncrypter->decrypt($entity);
        }
    }

    protected function supports(mixed $entity): bool
    {
        return is_a($entity, $this->entityClass, true);
    }
}
