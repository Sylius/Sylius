<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle;

use Sylius\Bundle\ProductBundle\DependencyInjection\Compiler\ServicesPass;
use Sylius\Bundle\ProductBundle\DependencyInjection\Compiler\ValidatorPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Product\Model\ArchetypeInterface;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeTranslationInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;
use Sylius\Component\Product\Model\OptionValueTranslationInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusProductBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ServicesPass());
        $container->addCompilerPass(new ValidatorPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            ProductInterface::class => 'sylius.model.product.class',
            ProductTranslationInterface::class => 'sylius.model.product_translation.class',
            AttributeInterface::class => 'sylius.model.product_attribute.class',
            AttributeTranslationInterface::class => 'sylius.model.product_attribute_translation.class',
            AttributeValueInterface::class => 'sylius.model.product_attribute_value.class',
            VariantInterface::class => 'sylius.model.product_variant.class',
            OptionInterface::class => 'sylius.model.product_option.class',
            OptionValueInterface::class => 'sylius.model.product_option_value.class',
            OptionValueTranslationInterface::class => 'sylius.model.product_option_value_translation.class',
            ArchetypeInterface::class => 'sylius.model.product_archetype.class',
            ProductAssociationInterface::class => 'sylius.model.product_association.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Product\Model';
    }
}
