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

    $parameters->set('sylius.behat.page.admin.customer_group.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.customer_group.index.class', '%sylius.behat.page.admin.crud.index.class%');
    $parameters->set('sylius.behat.page.admin.customer_group.update.class', '%sylius.behat.page.admin.crud.update.class%');

    $services
        ->set('sylius.behat.page.admin.customer_group.create', '%sylius.behat.page.admin.customer_group.create.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_customer_group_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.customer_group.index', '%sylius.behat.page.admin.customer_group.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_customer_group_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.customer_group.update', '%sylius.behat.page.admin.customer_group.update.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_customer_group_update'])
    ;
};
