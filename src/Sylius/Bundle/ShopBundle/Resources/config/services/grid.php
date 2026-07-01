<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.grid.account.order', \Sylius\Bundle\ShopBundle\Grid\Account\OrderGrid::class)
        ->args([
            '%sylius.model.order.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(\Sylius\Bundle\ShopBundle\Grid\Account\OrderGridInterface::class, 'sylius_shop.grid.account.order');

    $services->set('sylius_shop.grid.product', \Sylius\Bundle\ShopBundle\Grid\ProductGrid::class)
        ->args([
            '%sylius.model.product.class%',
        ])
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(\Sylius\Bundle\ShopBundle\Grid\ProductGridInterface::class, 'sylius_shop.grid.product');
};
