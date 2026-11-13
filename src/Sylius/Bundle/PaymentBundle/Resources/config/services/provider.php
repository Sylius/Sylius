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

use Sylius\Bundle\PaymentBundle\Provider\ActionsHttpResponseProvider;
use Sylius\Bundle\PaymentBundle\Provider\CompositeNotifyPaymentProvider;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProvider;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\DefaultPayloadProvider;
use Sylius\Bundle\PaymentBundle\Provider\DefaultPayloadProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\GatewayFactoryHttpResponseProvider;
use Sylius\Bundle\PaymentBundle\Provider\GatewayFactoryNameProvider;
use Sylius\Bundle\PaymentBundle\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\NotifyPaymentProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\NotifyResponseProvider;
use Sylius\Bundle\PaymentBundle\Provider\NotifyResponseProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProvider;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.provider.payment_request.gateway_factory_name', GatewayFactoryNameProvider::class);
    $services->alias(GatewayFactoryNameProviderInterface::class, 'sylius.provider.payment_request.gateway_factory_name');

    $services
        ->set('sylius.provider.payment_request', PaymentRequestProvider::class)
        ->args([service('sylius.repository.payment_request')])
    ;
    $services->alias(PaymentRequestProviderInterface::class, 'sylius.provider.payment_request');

    $services
        ->set('sylius.provider.payment_request.http_response.gateway_factory', GatewayFactoryHttpResponseProvider::class)
        ->args([
            service('sylius.provider.payment_request.gateway_factory_name'),
            tagged_locator('sylius.payment_request.provider.http_response', indexAttribute: 'gateway_factory'),
        ])
    ;
    $services->alias('sylius.provider.http_response.default', 'sylius.provider.payment_request.http_response.gateway_factory');

    $services
        ->set('sylius.provider.payment_request.default_action', DefaultActionProvider::class)
        ->args([
            service('sylius.repository.payment_method'),
            PaymentRequestInterface::ACTION_CAPTURE,
        ])
    ;
    $services->alias(DefaultActionProviderInterface::class, 'sylius.provider.payment_request.default_action');

    $services->set('sylius.provider.payment_request.default_payload', DefaultPayloadProvider::class);
    $services->alias(DefaultPayloadProviderInterface::class, 'sylius.provider.payment_request.default_payload');

    $services
        ->set('sylius.provider.payment_request.http_response.offline', ActionsHttpResponseProvider::class)
        ->args([tagged_locator('sylius.provider.payment_request.http_response.offline', indexAttribute: 'action')])
        ->tag('sylius.payment_request.provider.http_response', ['gateway_factory' => 'offline'])
    ;

    $services
        ->set('sylius.provider.payment_request.notify_payment', CompositeNotifyPaymentProvider::class)
        ->args([tagged_iterator('sylius.payment_request.payment_notify_provider')])
    ;
    $services->alias('sylius.provider.payment_request.notify_payment.composite', 'sylius.provider.payment_request.notify_payment');

    $services->alias(NotifyPaymentProviderInterface::class, 'sylius.provider.payment_request.notify_payment');

    $services->set('sylius.provider.payment_request.notify_response', NotifyResponseProvider::class);
    $services->alias(NotifyResponseProviderInterface::class, 'sylius.provider.payment_request.notify_response');
};
