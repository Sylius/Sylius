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

use Sylius\Behat\Context\Hook\BadGatewayContext;
use Sylius\Behat\Context\Hook\CacheContext;
use Sylius\Behat\Context\Hook\CalendarContext;
use Sylius\Behat\Context\Hook\DoctrineORMContext;
use Sylius\Behat\Context\Hook\GuestCartContext;
use Sylius\Behat\Context\Hook\MailerContext;
use Sylius\Behat\Context\Hook\SessionContext;
use Sylius\Behat\Context\Hook\TestThemeContext;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.hook.calendar', CalendarContext::class)
        ->args(['%sylius.behat.clock.date_file%'])
    ;

    $services
        ->set('sylius.behat.context.hook.doctrine_orm', DoctrineORMContext::class)
        ->args([service('doctrine.orm.entity_manager')])
    ;

    $services
        ->set('sylius.behat.context.hook.session', SessionContext::class)
        ->args([
            service('request_stack'),
            service('session.factory')->nullOnInvalid(),
        ])
    ;

    $services
        ->set('sylius.behat.context.hook.test_theme', TestThemeContext::class)
        ->args([service(TestThemeConfigurationManagerInterface::class)])
    ;

    $services
        ->set('sylius.behat.context.hook.mailer', MailerContext::class)
        ->args([service('test.mailer_pool')])
    ;

    $services
        ->set('sylius.behat.context.hook.cache', CacheContext::class)
        ->args([service('cache.app')])
    ;

    $services
        ->set('sylius.behat.context.hook.guest_cart', GuestCartContext::class)
        ->args(['%sylius.behat.guest_cart_token_file%'])
    ;

    $services->set('sylius.behat.context.hook.bad_gateway', BadGatewayContext::class);
};
