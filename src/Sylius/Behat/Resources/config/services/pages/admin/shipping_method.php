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

use Sylius\Behat\Page\Admin\ShippingMethod\CreatePage;
use Sylius\Behat\Page\Admin\ShippingMethod\IndexPage;
use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.shipping_method.create.class', CreatePage::class);
    $parameters->set('sylius.behat.page.admin.shipping_method.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.shipping_method.update.class', UpdatePage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.shipping_method.create', '%sylius.behat.page.admin.shipping_method.create.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_shipping_method_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.shipping_method.index', '%sylius.behat.page.admin.shipping_method.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_shipping_method_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.shipping_method.update', '%sylius.behat.page.admin.shipping_method.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_shipping_method_update'])
    ;
};
