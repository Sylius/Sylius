<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.console.command.cancel_unpaid_orders', 'Sylius\Bundle\CoreBundle\Console\Command\CancelUnpaidOrdersCommand')
        ->args([
            service('sylius.updater.unpaid_orders_state'),
            service('sylius.manager.order'),
            '%sylius_order.order_expiration_period%',
        ])
        ->tag('console.command');

    $services->set('sylius.console.command.check_requirements', 'Sylius\Bundle\CoreBundle\Console\Command\CheckRequirementsCommand')
        ->args([service('sylius.checker.installer.sylius_requirements')])
        ->tag('console.command');

    $services->set('sylius.console.command.price_history.clear', 'Sylius\Bundle\CoreBundle\PriceHistory\Console\Command\ClearPriceHistoryCommand')
        ->args([service('sylius.remover.channel_pricing_log_entries')])
        ->tag('console.command');

    $services->set('sylius.console.command.install_assets', 'Sylius\Bundle\CoreBundle\Console\Command\InstallAssetsCommand')
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            '%sylius_core.public_dir%',
        ])
        ->tag('console.command');

    $services->set('sylius.console.command.install', 'Sylius\Bundle\CoreBundle\Console\Command\InstallCommand')
        ->tag('console.command');

    $services->set('sylius.console.command.install_database', 'Sylius\Bundle\CoreBundle\Console\Command\InstallDatabaseCommand')
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            service('sylius.provider.installer.database_setup_commands'),
        ])
        ->tag('console.command');

    $services->set('sylius.console.command.install_sample_data', 'Sylius\Bundle\CoreBundle\Console\Command\InstallSampleDataCommand')
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            '%sylius_core.public_dir%',
        ])
        ->tag('console.command');

    $services->set('sylius.console.command.setup', 'Sylius\Bundle\CoreBundle\Console\Command\SetupCommand')
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
        ->tag('console.command');

    $services->set('sylius.console.command.inform_about_gus', 'Sylius\Bundle\CoreBundle\Console\Command\InformAboutGUSCommand')
        ->tag('console.command');

    $services->set('sylius.console.command.jwt_configuration', 'Sylius\Bundle\CoreBundle\Console\Command\JwtConfigurationCommand')
        ->tag('console.command');

    $services->set('sylius.console.command.show_plus_info', 'Sylius\Bundle\CoreBundle\Console\Command\ShowPlusInfoCommand')
        ->tag('console.command');
};
