<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Configurator\FactoryConfigurator;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CountryFactory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CurrencyFactory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CustomerFactory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CustomerGroupFactory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\FactoryWithModelClassAwareInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\LocaleFactory;

return static function (ContainerConfigurator $container) {
    $container->services()

        ->instanceof(FactoryWithModelClassAwareInterface::class)
            ->configurator([service('sylius.shop_fixtures.factory.configurator'), 'configure'])

        ->set('sylius.shop_fixtures.factory.configurator', FactoryConfigurator::class)
            ->args([
                service('sylius.resource_registry')
            ])

        ->set('sylius.shop_fixtures.factory.customer', CustomerFactory::class)
            ->args([
//                service('sylius.shop_fixtures.default_values.customer')
                service('sylius.shop_fixtures.transformer.customer')
            ])
            ->tag('foundry.factory')
        ->alias(CustomerFactory::class, 'sylius.shop_fixtures.factory.customer')

        ->set('sylius.shop_fixtures.factory.customer_group', CustomerGroupFactory::class)
//            ->args([
//                service('sylius.shop_fixtures.default_values.customer_group')
//            ])
            ->tag('foundry.factory')
        ->alias(CustomerGroupFactory::class, 'sylius.shop_fixtures.factory.customer_group')

        ->set('sylius.shop_fixtures.factory.country', CountryFactory::class)
//            ->args([
//                service('sylius.shop_fixtures.default_values.country')
//            ])
            ->tag('foundry.factory')
        ->alias(CountryFactory::class, 'sylius.shop_fixtures.factory.country')

        ->set('sylius.shop_fixtures.factory.currency', CurrencyFactory::class)
            ->args([
                service('sylius.shop_fixtures.default_values.currency')
            ])
            ->tag('foundry.factory')
        ->alias(CurrencyFactory::class, 'sylius.shop_fixtures.factory.currency')


        ->set('sylius.shop_fixtures.factory.locale', LocaleFactory::class)
//            ->args([
//                service('sylius.shop_fixtures.default_values.locale')
//            ])
            ->tag('foundry.factory')
        ->alias(LocaleFactory::class, 'sylius.shop_fixtures.factory.locale')
    ;
};
