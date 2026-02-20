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

use Sylius\Behat\Context\Ui\BrowserContext;
use Sylius\Behat\Context\Ui\ChannelContext;
use Sylius\Behat\Context\Ui\CustomerContext;
use Sylius\Behat\Context\Ui\ThemeContext;
use Sylius\Behat\Context\Ui\UserContext;
use Sylius\Behat\Context\Ui\EmailContext;
use Sylius\Behat\Context\Ui\SaveContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.ui.browser', BrowserContext::class)
        ->args([service('sylius.behat.element.browser')])
    ;

    $services
        ->set('sylius.behat.context.ui.channel', ChannelContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.channel_context_setter'),
            service('sylius.repository.channel'),
            service('sylius.behat.page.admin.channel.create'),
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.page.test_plugin.main'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.customer', CustomerContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.customer.show'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.theme', ThemeContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.channel.index'),
            service('sylius.behat.page.admin.channel.update'),
            service('sylius.behat.page.shop.home'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.user', UserContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.shop_user'),
            service('sylius.behat.page.admin.customer.show'),
            service('sylius.behat.page.shop.home'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.email', EmailContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.email_checker'),
            service('translator'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.save', SaveContext::class)
        ->args([service('sylius.behat.element.save')])
    ;
};
