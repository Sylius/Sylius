<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\ProductAttributeCannotBeRemoved;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductAttributeDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private ServiceRegistryInterface $attributeTypeRegistry,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ProductAttributeInterface;
    }

    /** @param ProductAttributeInterface $data */
    public function persist($data, array $context = [])
    {
        if (null === $data->getStorageType() && null !== $data->getType()) {
            /** @var AttributeTypeInterface $attributeType */
            $attributeType = $this->attributeTypeRegistry->get($data->getType());

            $data->setStorageType($attributeType->getStorageType());
        }

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
