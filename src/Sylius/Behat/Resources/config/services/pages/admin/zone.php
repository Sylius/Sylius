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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.zone.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.zone.index.class', '%sylius.behat.page.admin.crud.index.class%');
    $parameters->set('sylius.behat.page.admin.zone.update.class', '%sylius.behat.page.admin.crud.update.class%');

    $services
        ->set('sylius.behat.page.admin.zone.create', '%sylius.behat.page.admin.zone.create.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_zone_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.zone.index', '%sylius.behat.page.admin.zone.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_zone_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.zone.update', '%sylius.behat.page.admin.zone.update.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_zone_update'])
    ;
};
