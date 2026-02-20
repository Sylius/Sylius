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

use Sylius\Behat\Page\Admin\Order\IndexPage;
use Sylius\Behat\Page\Admin\Order\ShowPage;
use Sylius\Behat\Page\Admin\Order\UpdatePage;
use Sylius\Behat\Page\Admin\Order\HistoryPage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.order.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.order.show.class', ShowPage::class);
    $parameters->set('sylius.behat.page.admin.order.update.class', UpdatePage::class);
    $parameters->set('sylius.behat.page.admin.order.history.class', HistoryPage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.order.index', '%sylius.behat.page.admin.order.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_order_index',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.order.show', '%sylius.behat.page.admin.order.show.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')])
    ;

    $services
        ->set('sylius.behat.page.admin.order.update', '%sylius.behat.page.admin.order.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_order_update'])
    ;

    $services
        ->set('sylius.behat.page.admin.order.history', '%sylius.behat.page.admin.order.history.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;
};
