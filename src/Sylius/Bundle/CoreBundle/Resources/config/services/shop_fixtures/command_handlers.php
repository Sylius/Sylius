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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneCountryHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneCurrencyHandler;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler\CreateOneLocaleHandler;

return static function (ContainerConfigurator $container) {
    $container->services()

        ->set('sylius.shop_fixtures.command_handler.create_one_country', CreateOneCountryHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_currency', CreateOneCurrencyHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

        ->set('sylius.shop_fixtures.command_handler.create_one_locale', CreateOneLocaleHandler::class)
        ->tag( name: 'messenger.message_handler', attributes: ['bus' => 'sylius.shop_fixtures.command_bus'])

    ;
};
