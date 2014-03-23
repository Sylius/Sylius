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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Product management bundle with highly flexible architecture.
 * Implements basic product model with properties support.
 *
 * Use *SyliusVariableProductBundle* to get variants, options and
 * customizations support.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusProductBundle extends Bundle
{
    /**
     * Return array with currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Component\Product\Model\ProductInterface'        => 'sylius.model.product.class',
            'Sylius\Component\Product\Model\AttributeInterface'      => 'sylius.model.product_attribute.class',
            'Sylius\Component\Product\Model\AttributeValueInterface' => 'sylius.model.product_attribute_value.class',
            'Sylius\Component\Product\Model\VariantInterface'        => 'sylius.model.product_variant.class',
            'Sylius\Component\Product\Model\OptionInterface'         => 'sylius.model.product_option.class',
            'Sylius\Component\Product\Model\OptionValueInterface'    => 'sylius.model.product_option_value.class',
            'Sylius\Component\Product\Model\PrototypeInterface'      => 'sylius.model.product_prototype.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_product', $interfaces));

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Component\Product\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_product.driver.doctrine/orm'));
    }
}
