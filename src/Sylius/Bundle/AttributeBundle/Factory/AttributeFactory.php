<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Factory;

use Sylius\Component\Product\Model\Attribute;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Translation\Factory\TranslatableFactory;
use Sylius\Component\Translation\Factory\TranslatableFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeFactory implements TranslatableFactoryInterface
{
    /**
     * @var TranslatableFactory
     */
    private $translatableFactory;

    /**
     * @var ServiceRegistryInterface
     */
    private $attributeTypesRegistry;

    /**
     * @param TranslatableFactory $translatableFactory
     * @param ServiceRegistryInterface $attributeTypesRegistry
     */
    public function __construct(TranslatableFactory $translatableFactory, ServiceRegistryInterface $attributeTypesRegistry)
    {
        $this->translatableFactory = $translatableFactory;
        $this->attributeTypesRegistry = $attributeTypesRegistry;
    }

    /**
     * @param string $type
     *
     * @return Attribute
     */
    public function createTyped($type)
    {
        $attribute = $this->translatableFactory->createNew();
        $attribute->setType($type);
        $attribute->setStorageType($this->attributeTypesRegistry->get($type)->getStorageType());

        return $attribute;
    }

    /**
     * @return Attribute
     */
    public function createNew()
    {
        $attribute = $this->translatableFactory->createNew();
        $attribute->setStorageType($this->attributeTypesRegistry->get($attribute->getType())->getStorageType());

        return $attribute;
    }
}
