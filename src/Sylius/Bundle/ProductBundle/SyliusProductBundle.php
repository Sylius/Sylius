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
use Sylius\Bundle\TranslationBundle\AbstractTranslationBundle;
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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusProductBundle extends AbstractTranslationBundle
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
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'         => array(
                'ROLE_SYLIUS_PRODUCT_ADMIN',
                'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_ADMIN',
                'ROLE_SYLIUS_PRODUCT_PROTOTYPE_ADMIN',
                'ROLE_SYLIUS_PRODUCT_OPTION_ADMIN',
            ),
            'ROLE_SYLIUS_PRODUCT_ADMIN' => array(
                'ROLE_SYLIUS_PRODUCT_LIST',
                'ROLE_SYLIUS_PRODUCT_SHOW',
                'ROLE_SYLIUS_PRODUCT_CREATE',
                'ROLE_SYLIUS_PRODUCT_UPDATE',
                'ROLE_SYLIUS_PRODUCT_DELETE',
            ),
            'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_ADMIN' => array(
                'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_LIST',
                'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_SHOW',
                'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_CREATE',
                'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_UPDATE',
                'ROLE_SYLIUS_PRODUCT_ATTRIBUTE_DELETE',
            ),
            'ROLE_SYLIUS_PRODUCT_PROTOTYPE_ADMIN' => array(
                'ROLE_SYLIUS_PRODUCT_PROTOTYPE_LIST',
                'ROLE_SYLIUS_PRODUCT_PROTOTYPE_SHOW',
                'ROLE_SYLIUS_PRODUCT_PROTOTYPE_CREATE',
                'ROLE_SYLIUS_PRODUCT_PROTOTYPE_UPDATE',
                'ROLE_SYLIUS_PRODUCT_PROTOTYPE_DELETE',
            ),
            'ROLE_SYLIUS_PRODUCT_OPTION_ADMIN' => array(
                'ROLE_SYLIUS_PRODUCT_OPTION_LIST',
                'ROLE_SYLIUS_PRODUCT_OPTION_SHOW',
                'ROLE_SYLIUS_PRODUCT_OPTION_CREATE',
                'ROLE_SYLIUS_PRODUCT_OPTION_UPDATE',
                'ROLE_SYLIUS_PRODUCT_OPTION_DELETE',
            ),
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
            'Sylius\Component\Product\Model\ProductTranslationInterface' => 'sylius.model.product_translation.class',
            'Sylius\Component\Product\Model\AttributeInterface'      => 'sylius.model.product_attribute.class',
            'Sylius\Component\Product\Model\AttributeTranslationInterface'  => 'sylius.model.product_attribute_translation.class',
            'Sylius\Component\Product\Model\AttributeValueInterface' => 'sylius.model.product_attribute_value.class',
            'Sylius\Component\Product\Model\VariantInterface'        => 'sylius.model.product_variant.class',
            'Sylius\Component\Product\Model\OptionInterface'         => 'sylius.model.product_option.class',
            'Sylius\Component\Product\Model\OptionValueInterface'    => 'sylius.model.product_option_value.class',
            'Sylius\Component\Product\Model\PrototypeInterface'      => 'sylius.model.product_prototype.class',
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
