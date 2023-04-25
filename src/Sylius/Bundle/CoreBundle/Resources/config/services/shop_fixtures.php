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

use Sylius\Bundle\CoreBundle\ShopFixtures\Symfony\Messenger\CommandBusInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Symfony\Messenger\MessengerCommandBus;

return static function (ContainerConfigurator $container) {
    $container->import('shop_fixtures/command_handlers.php');
    $container->import('shop_fixtures/default_values.php');
    $container->import('shop_fixtures/factories.php');
    $container->import('shop_fixtures/fixtures.php');
    $container->import('shop_fixtures/stories.php');
    $container->import('shop_fixtures/transformers.php');
    $container->import('shop_fixtures/updaters.php');

    $container->services()

        ->set('sylius.shop_fixtures.bus.command', MessengerCommandBus::class)
            ->args([
                service('sylius.shop_fixtures.command_bus')
            ])
        ->alias(CommandBusInterface::class, 'sylius.shop_fixtures.bus.command')

    ;
};
