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

use Sylius\Bundle\ProductBundle\DependencyInjection\Compiler\ValidatorPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Product management bundle with highly flexible architecture.
 * Implements basic product model with properties support.
 *
 * Use *SyliusVariationBundle* to get variants, options and
 * customizations support.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusProductBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValidatorPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Product\Model\ProductInterface'        => 'sylius.model.product.class',
            'Sylius\Component\Product\Model\AttributeInterface'      => 'sylius.model.product_attribute.class',
            'Sylius\Component\Product\Model\AttributeValueInterface' => 'sylius.model.product_attribute_value.class',
            'Sylius\Component\Product\Model\VariantInterface'        => 'sylius.model.product_variant.class',
            'Sylius\Component\Product\Model\OptionInterface'         => 'sylius.model.product_option.class',
            'Sylius\Component\Product\Model\OptionValueInterface'    => 'sylius.model.product_option_value.class',
            'Sylius\Component\Product\Model\ArchetypeInterface'      => 'sylius.model.product_archetype.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Product\Model';
    }
}
