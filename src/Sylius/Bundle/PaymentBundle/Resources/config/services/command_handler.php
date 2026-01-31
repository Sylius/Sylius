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

use Sylius\Bundle\PaymentBundle\CommandHandler\Offline\CapturePaymentRequestHandler;
use Sylius\Bundle\PaymentBundle\CommandHandler\Offline\StatusPaymentRequestHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.command_handler.offline.capture_payment_request', CapturePaymentRequestHandler::class)
        ->args([
            service('sylius.provider.payment_request'),
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus']);

    $services->set('sylius.command_handler.offline.status_payment_request', StatusPaymentRequestHandler::class)
        ->args([
            service('sylius.provider.payment_request'),
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.payment_request.command_bus']);
};
