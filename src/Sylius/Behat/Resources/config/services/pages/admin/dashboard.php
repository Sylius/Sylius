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

use Sylius\Behat\Page\Admin\DashboardPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.dashboard.class', DashboardPage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.dashboard', '%sylius.behat.page.admin.dashboard.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')])
    ;
};
