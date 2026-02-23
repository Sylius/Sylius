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

use Sylius\Behat\Page\Admin\ProductAssociationType\IndexPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.product_association_type.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.product_association_type.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.product_association_type.update.class', '%sylius.behat.page.admin.crud.update.class%');

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.product_association_type.create', '%sylius.behat.page.admin.product_association_type.create.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_product_association_type_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_association_type.index', '%sylius.behat.page.admin.product_association_type.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_product_association_type_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_association_type.update', '%sylius.behat.page.admin.product_association_type.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_product_association_type_update'])
    ;
};
