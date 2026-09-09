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

use Sylius\Bundle\PaymentBundle\CommandProvider\ActionsCommandProvider;
use Sylius\Bundle\PaymentBundle\CommandProvider\GatewayFactoryCommandProvider;
use Sylius\Bundle\PaymentBundle\CommandProvider\Offline\CapturePaymentRequestCommandProvider;
use Sylius\Bundle\PaymentBundle\CommandProvider\Offline\StatusPaymentRequestCommandProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.command_provider.gateway_factory', GatewayFactoryCommandProvider::class)
        ->args([
            service('sylius.checker.payment_request_duplication'),
            service('sylius.provider.payment_request.gateway_factory_name'),
            tagged_locator('sylius.payment_request.command_provider', indexAttribute: 'gateway_factory'),
        ])
    ;
    $services->alias('sylius.command_provider.payment_request.default', 'sylius.command_provider.gateway_factory');

    $services
        ->set('sylius.command_provider.payment_request.offline', ActionsCommandProvider::class)
        ->args([tagged_locator('sylius.command_provider.payment_request.offline', indexAttribute: 'action')])
        ->tag('sylius.payment_request.command_provider', ['gateway_factory' => 'offline'])
    ;

    $services
        ->set('sylius.command_provider.payment_request.offline.capture', CapturePaymentRequestCommandProvider::class)
        ->tag('sylius.command_provider.payment_request.offline', ['action' => 'capture'])
    ;

    $services
        ->set('sylius.command_provider.payment_request.offline.status', StatusPaymentRequestCommandProvider::class)
        ->tag('sylius.command_provider.payment_request.offline', ['action' => 'status'])
    ;
};
