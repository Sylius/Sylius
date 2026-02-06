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

use Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Sylius\Bundle\CoreBundle\Installer\Checker\SyliusRequirementsChecker;
use Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProvider;
use Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProviderInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\ChannelSetup;
use Sylius\Bundle\CoreBundle\Installer\Setup\ChannelSetupInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetup;
use Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetupInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetup;
use Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetupInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius_installer_currency', 'USD');

    $services->set('sylius.checker.installer.command_directory', CommandDirectoryChecker::class)
        ->args([service('filesystem')]);

    $services->set('sylius.checker.installer.sylius_requirements', SyliusRequirementsChecker::class)
        ->args([service('sylius.requirements.installer.sylius')]);

    $services->set('sylius.provider.installer.database_setup_commands', DatabaseSetupCommandsProvider::class)
        ->args([service('doctrine.orm.entity_manager')]);

    $services->alias(DatabaseSetupCommandsProviderInterface::class, 'sylius.provider.installer.database_setup_commands');

    $services->set('sylius.setup.installer.currency', CurrencySetup::class)
        ->args([
            service('sylius.repository.currency'),
            service('sylius.factory.currency'),
            '%sylius_installer_currency%',
        ]);

    $services->alias(CurrencySetupInterface::class, 'sylius.setup.installer.currency');

    $services->set('sylius.setup.installer.locale', LocaleSetup::class)
        ->args([
            service('sylius.repository.locale'),
            service('sylius.factory.locale'),
            '%locale%',
            service('filesystem'),
        ]);

    $services->alias(LocaleSetupInterface::class, 'sylius.setup.installer.locale');

    $services->set('sylius.setup.installer.channel', ChannelSetup::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.factory.channel'),
            service('sylius.manager.channel'),
        ]);

    $services->alias(ChannelSetupInterface::class, 'sylius.setup.installer.channel');
};
