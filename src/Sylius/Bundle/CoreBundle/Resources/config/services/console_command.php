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

use Sylius\Bundle\CoreBundle\Console\Command\CancelUnpaidOrdersCommand;
use Sylius\Bundle\CoreBundle\Console\Command\CheckRequirementsCommand;
use Sylius\Bundle\CoreBundle\Console\Command\InformAboutGUSCommand;
use Sylius\Bundle\CoreBundle\Console\Command\InstallAssetsCommand;
use Sylius\Bundle\CoreBundle\Console\Command\InstallCommand;
use Sylius\Bundle\CoreBundle\Console\Command\InstallDatabaseCommand;
use Sylius\Bundle\CoreBundle\Console\Command\InstallSampleDataCommand;
use Sylius\Bundle\CoreBundle\Console\Command\JwtConfigurationCommand;
use Sylius\Bundle\CoreBundle\Console\Command\SetupCommand;
use Sylius\Bundle\CoreBundle\Console\Command\ShowPlusInfoCommand;
use Sylius\Bundle\CoreBundle\PriceHistory\Console\Command\ClearPriceHistoryCommand;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.console.command.cancel_unpaid_orders', CancelUnpaidOrdersCommand::class)
        ->args([
            service('sylius.updater.unpaid_orders_state'),
            service('sylius.manager.order'),
            '%sylius_order.order_expiration_period%',
        ])
        ->tag('console.command')
    ;

    $services
        ->set('sylius.console.command.check_requirements', CheckRequirementsCommand::class)
        ->args([service('sylius.checker.installer.sylius_requirements')])
        ->tag('console.command')
    ;

    $services
        ->set('sylius.console.command.price_history.clear', ClearPriceHistoryCommand::class)
        ->args([service('sylius.remover.channel_pricing_log_entries')])
        ->tag('console.command')
    ;

    $services
        ->set('sylius.console.command.install_assets', InstallAssetsCommand::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            '%sylius_core.public_dir%',
        ])
        ->tag('console.command')
    ;

    $services->set('sylius.console.command.install', InstallCommand::class)->tag('console.command');

    $services
        ->set('sylius.console.command.install_database', InstallDatabaseCommand::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            service('sylius.provider.installer.database_setup_commands'),
        ])
        ->tag('console.command')
    ;

    $services
        ->set('sylius.console.command.install_sample_data', InstallSampleDataCommand::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            '%sylius_core.public_dir%',
        ])
        ->tag('console.command')
    ;

    $services
        ->set('sylius.console.command.setup', SetupCommand::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            service('sylius.setup.installer.currency'),
            service('sylius.setup.installer.locale'),
            service('sylius.setup.installer.channel'),
            service('sylius.factory.admin_user'),
            service('sylius.repository.admin_user'),
            service('validator'),
        ])
        ->tag('console.command')
    ;

    $services->set('sylius.console.command.inform_about_gus', InformAboutGUSCommand::class)->tag('console.command');

    $services->set('sylius.console.command.jwt_configuration', JwtConfigurationCommand::class)->tag('console.command');

    $services->set('sylius.console.command.show_plus_info', ShowPlusInfoCommand::class)->tag('console.command');
};
