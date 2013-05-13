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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Product management bundle with highly flexible architecture.
 * Implements basic product model with properties support.
 *
 * Use *SyliusCustomizableProductBundle* to get variants, options and
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
            'Sylius\Bundle\ProductBundle\Model\ProductInterface'                  => 'sylius.model.product.class',
            'Sylius\Bundle\ProductBundle\Model\Property\PropertyInterface'        => 'sylius.model.property.class',
            'Sylius\Bundle\ProductBundle\Model\Property\ProductPropertyInterface' => 'sylius.model.product_property.class',
            'Sylius\Bundle\ProductBundle\Model\Prototype\PrototypeInterface'      => 'sylius.model.prototype.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_product', $interfaces));
    }
}
