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

use Sylius\Bundle\PayumBundle\PaymentRequest\Command\AuthorizePaymentRequest;
use Sylius\Bundle\PayumBundle\PaymentRequest\Command\CapturePaymentRequest;
use Sylius\Bundle\PayumBundle\PaymentRequest\Command\NotifyPaymentRequest;
use Sylius\Bundle\PayumBundle\PaymentRequest\Command\StatusPaymentRequest;
use Sylius\Bundle\PayumBundle\PaymentRequest\CommandHandler\ModelPaymentRequestHandler;
use Sylius\Bundle\PayumBundle\PaymentRequest\CommandHandler\TokenPaymentRequestHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_payum.command_handler.payment_request.token', TokenPaymentRequestHandler::class)
        ->abstract()
        ->args([
            service('sylius.provider.payment_request'),
            service('sylius_payum.resolver.payment_request.doctrine_proxy_object'),
            service('sylius_payum.factory.payment_request.payum_token'),
            service('sylius_payum.processor.payment_request.request'),
            service('sylius_payum.processor.payment_request.after_token_request'),
        ]);

    $services->set('sylius_payum.command_handler.payment_request.token.capture')
        ->parent('sylius_payum.command_handler.payment_request.token')
        ->args([service('sylius_payum.factory.capture')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus', 'handles' => CapturePaymentRequest::class]);

    $services->set('sylius_payum.command_handler.payment_request.token.authorize')
        ->parent('sylius_payum.command_handler.payment_request.token')
        ->args([service('sylius_payum.factory.authorize')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus', 'handles' => AuthorizePaymentRequest::class]);

    $services->set('sylius_payum.command_handler.payment_request.model', ModelPaymentRequestHandler::class)
        ->abstract()
        ->args([
            service('sylius.provider.payment_request'),
            service('sylius_payum.resolver.payment_request.doctrine_proxy_object'),
            service('sylius_payum.processor.payment_request.request'),
        ]);

    $services->set('sylius_payum.command_handler.payment_request.model.status')
        ->parent('sylius_payum.command_handler.payment_request.model')
        ->args([service('sylius_payum.factory.get_status')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus', 'handles' => StatusPaymentRequest::class]);

    $services->set('sylius_payum.command_handler.payment_request.model.notify')
        ->parent('sylius_payum.command_handler.payment_request.model')
        ->args([service('sylius_payum.factory.get_status')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus', 'handles' => NotifyPaymentRequest::class]);
};
