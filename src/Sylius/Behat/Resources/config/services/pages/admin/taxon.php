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

    $parameters->set('sylius.behat.page.admin.taxon.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.taxon.create_for_parent.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.taxon.update.class', '%sylius.behat.page.admin.crud.update.class%');

    $services
        ->set('sylius.behat.page.admin.taxon.create', '%sylius.behat.page.admin.taxon.create.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_taxon_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.taxon.create_for_parent', '%sylius.behat.page.admin.taxon.create_for_parent.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_taxon_create_for_parent'])
    ;

    $services
        ->set('sylius.behat.page.admin.taxon.update', '%sylius.behat.page.admin.taxon.update.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_taxon_update'])
    ;
};
