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

use Sylius\Bundle\AdminBundle\Menu\AdministrationMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\CatalogMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ConfigurationMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\CustomersMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\DashboardMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\EventMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\MainMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\MarketingMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\OfficialSupportMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\SalesMenuBuilder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.menu_builder.main', MainMenuBuilder::class)
        ->args([
            service('knp_menu.factory'),
            service('event_dispatcher'),
            service('router'),
        ])
        ->tag('knp_menu.menu_builder', ['method' => 'createMenu', 'alias' => 'sylius_admin.main'])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.dashboard', DashboardMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: 9999)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.catalog', CatalogMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -100)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.sales', SalesMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -200)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.customers', CustomersMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -300)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.marketing', MarketingMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -400)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.configuration', ConfigurationMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -500)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.official_support', OfficialSupportMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -600)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.administration', AdministrationMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -700)
        ->args([
            service('.inner'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.event', EventMenuBuilder::class)
        ->decorate('sylius_admin.menu_builder.main', priority: -9999)
        ->args([
            service('.inner'),
            service('knp_menu.factory'),
            service('event_dispatcher'),
        ])
    ;
};
