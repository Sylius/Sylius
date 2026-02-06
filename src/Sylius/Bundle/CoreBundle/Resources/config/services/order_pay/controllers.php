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

use Sylius\Bundle\CoreBundle\OrderPay\Action\PaymentRequestPayAction;
use Sylius\Bundle\CoreBundle\OrderPay\Controller\OrderPayController;
use Sylius\Component\Resource\Metadata\MetadataInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.controller.order_pay', OrderPayController::class)
        ->abstract()
        ->args([
            service('sylius.repository.order'),
            inline_service(MetadataInterface::class)
                ->args(['sylius.order'])
                ->factory([service('sylius.resource_registry'), 'get']),
            service('sylius.resource_controller.request_configuration_factory'),
        ]);

    $services->set('sylius.controller.payment_request_pay', PaymentRequestPayAction::class)
        ->abstract()
        ->args([
            inline_service(MetadataInterface::class)
                ->args(['sylius.payment_request'])
                ->factory([service('sylius.resource_registry'), 'get']),
            service('sylius.resource_controller.request_configuration_factory'),
            service('sylius.repository.payment_request'),
            service('sylius.processor.payment_request.http_response'),
        ]);
};
