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

use Sylius\Behat\Page\Admin\Inventory\IndexPage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.inventory.index.class', IndexPage::class);

    $services
        ->set('sylius.behat.page.admin.inventory.index', '%sylius.behat.page.admin.inventory.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_inventory_index',
            service(AutocompleteHelperInterface::class),
        ])
    ;
};
