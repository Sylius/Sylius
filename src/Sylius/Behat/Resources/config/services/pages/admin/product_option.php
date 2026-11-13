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

    $parameters->set('sylius.behat.page.admin.product_option.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.product_option.update.class', '%sylius.behat.page.admin.crud.update.class%');
    $parameters->set('sylius.behat.page.admin.product_option.index.class', '%sylius.behat.page.admin.crud.index.class%');

    $services
        ->set('sylius.behat.page.admin.product_option.create', '%sylius.behat.page.admin.product_option.create.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_product_option_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_option.update', '%sylius.behat.page.admin.product_option.update.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_product_option_update'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_option.index', '%sylius.behat.page.admin.product_option.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_product_option_index'])
    ;
};
