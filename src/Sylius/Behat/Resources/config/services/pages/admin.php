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

use Sylius\Behat\Page\Admin\Crud\CreatePage;
use Sylius\Behat\Page\Admin\Crud\IndexPage;
use Sylius\Behat\Page\Admin\Crud\UpdatePage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $container->import('admin/**/*.php');

    $parameters->set('sylius.behat.page.admin.crud.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.crud.create.class', CreatePage::class);
    $parameters->set('sylius.behat.page.admin.crud.update.class', UpdatePage::class);

    $services
        ->set('sylius.behat.page.admin.crud.index', '%sylius.behat.page.admin.crud.index.class%')
        ->abstract()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')])
    ;

    $services
        ->set('sylius.behat.page.admin.crud.create', '%sylius.behat.page.admin.crud.create.class%')
        ->abstract()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.admin.crud.update', '%sylius.behat.page.admin.crud.update.class%')
        ->abstract()
        ->parent('sylius.behat.symfony_page')
    ;
};
