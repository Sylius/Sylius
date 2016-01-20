<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Factory;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeFactory implements AttributeFactoryInterface
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
    public function createTyped($type)
    {
        $attribute = $this->factory->createNew();
        $attribute->setType($type);
        $attribute->setStorageType($this->attributeTypesRegistry->get($type)->getStorageType());

        return $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $attribute = $this->factory->createNew();
        $attribute->setStorageType($this->attributeTypesRegistry->get($attribute->getType())->getStorageType());

        return $attribute;
    }
}
