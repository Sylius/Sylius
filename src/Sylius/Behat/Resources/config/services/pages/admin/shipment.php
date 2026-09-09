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

use Sylius\Behat\Page\Admin\Shipment\IndexPage;
use Sylius\Behat\Page\Admin\Shipment\ShowPage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.shipment.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.shipment.show.class', ShowPage::class);

    $services
        ->set('sylius.behat.page.admin.shipment.index', '%sylius.behat.page.admin.shipment.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_shipment_index',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.shipment.show', '%sylius.behat.page.admin.shipment.show.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
