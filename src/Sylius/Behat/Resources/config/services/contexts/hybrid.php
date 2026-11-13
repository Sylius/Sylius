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

use Sylius\Behat\Context\Hybrid\Setup\CartContext;
use Sylius\Behat\Context\Hybrid\Setup\SecurityContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.hybrid.shop.composite_cart', CartContext::class)
        ->args([
            service('sylius.behat.context.api.shop.cart'),
            service('sylius.behat.context.ui.shop.cart'),
        ])
    ;

    $services
        ->set('sylius.behat.context.hybrid.shop.composite_customer', SecurityContext::class)
        ->args([
            service('sylius.behat.context.setup.shop_security'),
            service('sylius.behat.context.setup.shop_api_security'),
            service('sylius.behat.shared_storage'),
        ])
    ;
};
