<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius_installer_currency', 'USD');

    $services->set('sylius.checker.installer.command_directory', 'Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker')
        ->args([service('filesystem')]);

    $services->set('sylius.checker.installer.sylius_requirements', 'Sylius\Bundle\CoreBundle\Installer\Checker\SyliusRequirementsChecker')
        ->args([service('sylius.requirements.installer.sylius')]);

    $services->set('sylius.provider.installer.database_setup_commands', 'Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProvider')
        ->args([service('doctrine.orm.entity_manager')]);

    $services->alias('Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProviderInterface', 'sylius.provider.installer.database_setup_commands');

    $services->set('sylius.setup.installer.currency', 'Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetup')
        ->args([
            service('sylius.repository.currency'),
            service('sylius.factory.currency'),
            '%sylius_installer_currency%',
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetupInterface', 'sylius.setup.installer.currency');

    $services->set('sylius.setup.installer.locale', 'Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetup')
        ->args([
            service('sylius.repository.locale'),
            service('sylius.factory.locale'),
            '%locale%',
            service('filesystem'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetupInterface', 'sylius.setup.installer.locale');

    $services->set('sylius.setup.installer.channel', 'Sylius\Bundle\CoreBundle\Installer\Setup\ChannelSetup')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.factory.channel'),
            service('sylius.manager.channel'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Installer\Setup\ChannelSetupInterface', 'sylius.setup.installer.channel');
};
