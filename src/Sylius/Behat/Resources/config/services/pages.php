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

use FriendsOfBehat\PageObjectExtension\Page\Page;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Page\ErrorPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $container->import('pages/admin.php');
    $container->import('pages/shop.php');
    $container->import('pages/test_plugin.php');

    $parameters->set('sylius.behat.page.error.class', ErrorPage::class);

    $services
        ->set('sylius.behat.page', Page::class)
        ->abstract()
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ])
    ;

    $services
        ->set('sylius.behat.symfony_page', SymfonyPage::class)
        ->abstract()
        ->parent('sylius.behat.page')
        ->args([service('router')])
    ;

    $services
        ->set('sylius.behat.page.error', '%sylius.behat.page.error.class%')
        ->parent('sylius.behat.page')
    ;
};
