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

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.controller.order_pay', OrderPayController::class)
        ->args([
            service('sylius.repository.order'),
        ])
        ->abstract()
    ;

    $services
        ->set('sylius.controller.payment_request_pay', PaymentRequestPayAction::class)
        ->args([
            service('sylius.repository.payment_request'),
            service('sylius.processor.payment_request.http_response'),
        ])
        ->abstract()
    ;
};
