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

use Sylius\Behat\Context\Cli\CancelUnpaidOrdersContext;
use Sylius\Behat\Context\Cli\ChangeAdminPasswordContext;
use Sylius\Behat\Context\Cli\CreateAdminUserContext;
use Sylius\Behat\Context\Cli\InstallerContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.cli.installer', InstallerContext::class)
        ->args([
            service('kernel'),
            service('doctrine.orm.entity_manager'),
            service('sylius.checker.installer.command_directory'),
            service('sylius.setup.installer.currency'),
            service('sylius.setup.installer.locale'),
            service('sylius.setup.installer.channel'),
            service('sylius.setup.installer.country'),
            service('sylius.setup.installer.zone'),
            service('sylius.setup.installer.channel_default_tax_zone'),
            service('sylius.factory.admin_user'),
            service('sylius.repository.admin_user'),
            service('validator'),
            '%sylius_core.public_dir%',
        ])
    ;

    $services
        ->set('sylius.behat.context.cli.cancel_unpaid_orders', CancelUnpaidOrdersContext::class)
        ->args([
            service('kernel'),
            service('sylius.repository.order'),
        ])
    ;

    $services
        ->set('sylius.behat.context.cli.change_admin_password', ChangeAdminPasswordContext::class)
        ->args([
            service('kernel'),
            service('sylius.repository.admin_user'),
            service('security.user_password_hasher'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.cli.create_admin_user', CreateAdminUserContext::class)
        ->args([
            service('kernel'),
            service('sylius.repository.admin_user'),
        ])
    ;
};
