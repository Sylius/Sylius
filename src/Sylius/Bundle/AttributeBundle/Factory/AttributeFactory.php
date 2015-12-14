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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Translation\Factory\TranslatableFactory;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeFactory extends TranslatableFactory
{
    /**
     * @var ServiceRegistryInterface
     */
    private $attributeTypesRegistry;

    /**
     * @param FactoryInterface $factory
     * @param LocaleProviderInterface $localeProvider
     * @param ServiceRegistryInterface $attributeTypesRegistry
     */
    public function __construct(FactoryInterface $factory, LocaleProviderInterface $localeProvider, ServiceRegistryInterface $attributeTypesRegistry)
    {
        parent::__construct($factory, $localeProvider);

        $this->attributeTypesRegistry = $attributeTypesRegistry;
    }

    /**
     * @param string $type
     *
     * @return Attribute
     */
    public function createTyped($type)
    {
        $attribute = parent::createNew();
        $attribute->setType($type);
        $attribute->setStorageType($this->attributeTypesRegistry->get($type)->getStorageType());

        return $attribute;
    }

    /**
     * @return Attribute
     */
    public function createNew()
    {
        $attribute = parent::createNew();
        $attribute->setStorageType($this->attributeTypesRegistry->get($attribute->getType())->getStorageType());

        return $attribute;
    }
}
