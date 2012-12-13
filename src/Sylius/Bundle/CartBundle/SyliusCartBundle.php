<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Simple and flexible cart system.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartBundle extends Bundle
{
    /**
     * Return array of currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\CartBundle\Model\CartInterface'     => 'sylius_cart.model.cart.class',
            'Sylius\Bundle\CartBundle\Model\CartItemInterface' => 'sylius_cart.model.item.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_cart', $interfaces));
    }
}
