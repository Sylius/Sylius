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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Http\Client\ClientInterface;
use Sylius\Bundle\PayumBundle\Client\PaypalHeaderInjectingClient;
use Sylius\Bundle\PayumBundle\HttpClient\HttpClient;

return static function (ContainerConfigurator $container) {
    $container->import('services/**.php');
    $container->import('services/payment_request/**/*.php');

    $services = $container->services();

    $services->set('sylius_payum.http_client', HttpClient::class)->args([service(ClientInterface::class)]);
    $services
        ->set('sylius_payum.http_client.paypal_header_injecting', PaypalHeaderInjectingClient::class)
        ->decorate('sylius_payum.http_client')
        ->args([service('.inner')])
    ;
};
