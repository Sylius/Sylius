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

use Sylius\Bundle\PaymentBundle\Action\PaymentMethodNotifyAction;
use Sylius\Bundle\PaymentBundle\Action\PaymentRequestNotifyAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.controller.payment_request_notify', PaymentRequestNotifyAction::class)
        ->args([
            service('sylius.repository.payment_request'),
            service('sylius.checker.finalized_payment_request'),
            service('sylius.processor.payment_request.notify_payload'),
            service('sylius.manager.payment_request'),
            service('sylius.announcer.payment_request'),
            service('sylius.provider.payment_request.notify_response'),
        ]);

    $services->set('sylius.controller.payment_method_notify', PaymentMethodNotifyAction::class)
        ->args([
            service('sylius.repository.payment_method'),
            service('sylius.provider.payment_request.notify_payment'),
            service('sylius.factory.payment_request'),
            service('sylius.processor.payment_request.notify_payload'),
            service('sylius.repository.payment_request'),
            service('sylius.announcer.payment_request'),
            service('sylius.provider.payment_request.notify_response'),
        ]);
};
