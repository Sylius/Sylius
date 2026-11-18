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

use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Common\ResponseContext;
use Sylius\Behat\Context\Api\Common\SaveContext;
use Sylius\Behat\Context\Api\DebugContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.api.admin.save', SaveContext::class)
        ->args([service('sylius.behat.api_platform_client.admin')])
    ;

    $services
        ->set('sylius.behat.context.api.shop.save', SaveContext::class)
        ->args([service('sylius.behat.api_platform_client.shop')])
    ;

    $services
        ->set('sylius.behat.context.api.admin.response', ResponseContext::class)
        ->args([
            service(ResponseCheckerInterface::class),
            service('sylius.behat.api_platform_client.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.response', ResponseContext::class)
        ->args([
            service(ResponseCheckerInterface::class),
            service('sylius.behat.api_platform_client.shop'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.debug', DebugContext::class)
        ->args([service(ResponseCheckerInterface::class)])
    ;
};
