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

use Sylius\Bundle\AdminBundle\Menu\CompositeMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\MainMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\AdministrationMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\CatalogMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\ConfigurationMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\CustomersMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\DashboardMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\EventMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\MarketingMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\OfficialSupportMenuProvider;
use Sylius\Bundle\AdminBundle\Menu\Provider\Main\SalesMenuProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.menu_builder.main.composite', CompositeMenuBuilder::class)
        ->args([
            service('knp_menu.factory'),
            tagged_iterator('sylius_admin.main_menu_provider'),
        ])
    ;

    $services
        ->set('sylius_admin.menu_builder.main', MainMenuBuilder::class)
        ->args([
            service('sylius_admin.menu_builder.main.composite'),
        ])
        ->tag('knp_menu.menu_builder', ['method' => 'createMenu', 'alias' => 'sylius_admin.main'])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.dashboard', DashboardMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -100])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.catalog', CatalogMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -200])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.sales', SalesMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -300])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.customers', CustomersMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -400])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.marketing', MarketingMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -500])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.configuration', ConfigurationMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -600])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.official_support', OfficialSupportMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -700])
    ;

    $services
        ->set('sylius_admin.menu_builder.provider.main.administration', AdministrationMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -800])
    ;

    $services
        ->set('sylius_admin.menu_builder.main.event', EventMenuProvider::class)
        ->tag('sylius_admin.main_menu_provider', ['priority' => -9999])
        ->args([
            service('knp_menu.factory'),
            service('event_dispatcher'),
        ])
    ;
};
