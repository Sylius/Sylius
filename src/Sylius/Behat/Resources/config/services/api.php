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

use Sylius\Behat\Client\ApiBrowserFactory;
use Sylius\Behat\Client\ApiPlatformClient;
use Sylius\Behat\Client\ApiPlatformSecurityClient;
use Sylius\Behat\Client\ContentTypeGuide;
use Sylius\Behat\Client\RequestFactory;
use Sylius\Behat\Client\ResponseChecker;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Client\WorkerHttpBrowser;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(WorkerHttpBrowser::class);

    $services
        ->set(ApiBrowserFactory::class)
        ->args([
            service('test.client'),
            service(WorkerHttpBrowser::class),
        ])
    ;

    $services
        ->set('sylius.behat.api_browser', AbstractBrowser::class)
        ->factory([service(ApiBrowserFactory::class), 'createBrowser'])
    ;

    $services
        ->set('sylius.behat.api_platform_client', ApiPlatformClient::class)
        ->abstract()
        ->args([
            service('sylius.behat.api_browser'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.request_factory'),
            service(ResponseCheckerInterface::class),
            '%sylius.api.authorization_header%',
        ])
    ;

    $services
        ->set('sylius.behat.api_platform_client.shop', ApiPlatformClient::class)
        ->parent('sylius.behat.api_platform_client')
        ->args(['shop'])
    ;

    $services
        ->set('sylius.behat.api_platform_client.admin', ApiPlatformClient::class)
        ->parent('sylius.behat.api_platform_client')
        ->args(['admin'])
    ;

    $services->set(ResponseCheckerInterface::class, ResponseChecker::class);

    $services
        ->set('sylius.behat.client.admin_api_platform_security_client', ApiPlatformSecurityClient::class)
        ->args([
            service('sylius.behat.api_browser'),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
            'admin/administrators/token',
        ])
    ;

    $services
        ->set('sylius.behat.client.shop_api_platform_security_client', ApiPlatformSecurityClient::class)
        ->args([
            service('sylius.behat.api_browser'),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
            'shop/customers/token',
        ])
    ;

    $services->set('sylius.behat.content_type_guide', ContentTypeGuide::class);

    $services
        ->set('sylius.behat.request_factory', RequestFactory::class)
        ->args([
            service('sylius.behat.content_type_guide'),
            '%sylius.security.api_route%',
        ])
    ;
};
