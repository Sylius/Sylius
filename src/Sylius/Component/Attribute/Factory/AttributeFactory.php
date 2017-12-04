<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Attribute\Factory;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class AttributeFactory implements AttributeFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ServiceRegistryInterface
     */
    private $attributeTypesRegistry;

    /**
     * @param FactoryInterface $factory
     * @param ServiceRegistryInterface $attributeTypesRegistry
     */
    public function __construct(FactoryInterface $factory, ServiceRegistryInterface $attributeTypesRegistry)
    {
        $this->factory = $factory;
        $this->attributeTypesRegistry = $attributeTypesRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): AttributeInterface
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createTyped(string $type): AttributeInterface
    {
        /** @var AttributeTypeInterface $attributeType */
        $attributeType = $this->attributeTypesRegistry->get($type);

        /** @var AttributeInterface $attribute */
        $attribute = $this->factory->createNew();
        $attribute->setType($type);
        $attribute->setStorageType($attributeType->getStorageType());

        return $attribute;
    }
}
