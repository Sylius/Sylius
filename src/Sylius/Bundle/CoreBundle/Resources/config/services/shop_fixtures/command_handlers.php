<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateManyAddressesHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateManyShopUsersHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneAddressHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneCountryHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneCurrencyHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneCustomerGroupHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneCustomerHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneLocaleHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneShopUserHandler;

return static function (ContainerConfigurator $container) {
    $container->services()

        ->set('sylius.shop_fixtures.command_handler.create_one_address', CreateOneAddressHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_customer', CreateOneCustomerHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_customer_group', CreateOneCustomerGroupHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_country', CreateOneCountryHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_currency', CreateOneCurrencyHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_locale', CreateOneLocaleHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_shop_user', CreateOneShopUserHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_many_addresses', CreateManyAddressesHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_many_shop_users', CreateManyShopUsersHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])
    ;
};
