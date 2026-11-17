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

use Sylius\Bundle\CoreBundle\OrderPay\Provider\FinalUrlProvider;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\NoPaymentPayResponseProvider;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\PaymentRequestAfterPayResponseProvider;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\PaymentRequestPayResponseProvider;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\UrlProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.provider.order_pay.payment_request_pay_url', UrlProvider::class)
        ->args([service('sylius.processor.order_pay.route_parameters')])
        ->abstract()
    ;

    $services
        ->set('sylius.provider.order_pay.after_pay_url', UrlProvider::class)
        ->args([service('sylius.processor.order_pay.route_parameters')])
        ->abstract()
    ;

    $services
        ->set('sylius.provider.order_pay.final_url', FinalUrlProvider::class)
        ->args([service('sylius.processor.order_pay.route_parameters')])
        ->abstract()
    ;

    $services
        ->set('sylius.provider.order_pay.pay_response.payment_request', PaymentRequestPayResponseProvider::class)
        ->args([
            service('sylius.factory.payment_request'),
            service('sylius.repository.payment_request'),
            service('sylius.provider.payment_request.default_action'),
            service('sylius.provider.payment_request.default_payload'),
            service('sylius.checker.finalized_payment_request'),
        ])
        ->abstract()
    ;

    $services
        ->set('sylius.provider.order_pay.pay_response.no_payment', NoPaymentPayResponseProvider::class)
        ->abstract()
    ;

    $services
        ->set('sylius.provider.order_pay.after_pay_response.payment_request', PaymentRequestAfterPayResponseProvider::class)
        ->args([
            service('sylius.factory.payment_request'),
            service('sylius.processor.payment_request.http_response'),
            service('sylius.repository.payment_request'),
        ])
        ->abstract()
    ;
};
