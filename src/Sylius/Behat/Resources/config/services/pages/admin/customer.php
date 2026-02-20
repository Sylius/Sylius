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

use Sylius\Behat\Page\Admin\Customer\IndexPage;
use Sylius\Behat\Page\Admin\Customer\ShowPage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.customer.create.class', '%sylius.behat.page.admin.crud.create.class%');
    $parameters->set('sylius.behat.page.admin.customer.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.customer.order_index.class', '%sylius.behat.page.admin.crud.index.class%');
    $parameters->set('sylius.behat.page.admin.customer.update.class', '%sylius.behat.page.admin.crud.update.class%');
    $parameters->set('sylius.behat.page.admin.customer.show.class', ShowPage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.customer.create', '%sylius.behat.page.admin.customer.create.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_customer_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.customer.index', '%sylius.behat.page.admin.customer.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_customer_index',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.customer.order_index', '%sylius.behat.page.admin.customer.order_index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_customer_order_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.customer.update', '%sylius.behat.page.admin.customer.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_customer_update'])
    ;

    $services
        ->set('sylius.behat.page.admin.customer.show', '%sylius.behat.page.admin.customer.show.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;
};
