<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\ProductAttributeCannotBeRemoved;
use Sylius\Component\Product\Model\ProductAttributeInterface;

/** @experimental */
final class ProductAttributeDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ProductAttributeInterface;
    }

    /** @param ProductAttributeInterface $data */
    public function persist($data, array $context = [])
    {
        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        try {
            return $this->decoratedDataPersister->remove($data, $context);
        } catch (ForeignKeyConstraintViolationException) {
            throw new ProductAttributeCannotBeRemoved();
        }
    }
}
