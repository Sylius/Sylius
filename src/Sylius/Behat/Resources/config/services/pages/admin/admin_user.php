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

use Sylius\Behat\Page\Admin\Administrator\CreatePage;
use Sylius\Behat\Page\Admin\Administrator\UpdatePage;
use Sylius\Behat\Page\Admin\Crud\IndexPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.page.admin.administrator.create', CreatePage::class)
        ->parent('sylius.behat.page.admin.crud.create')
        ->args([
            'sylius_admin_admin_user_create',
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.administrator.index', IndexPage::class)
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_admin_user_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.administrator.update', UpdatePage::class)
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_admin_user_update'])
    ;
};
