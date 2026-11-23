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

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.provider.order_pay.payment_request_pay_url')
        ->parent('sylius.provider.order_pay.payment_request_pay_url')
        ->args([
            '%sylius_shop.order_pay.payment_request_pay_route%',
            '%sylius_shop.order_pay.payment_request_pay_route_parameters%',
        ])
    ;

    $services
        ->set('sylius_shop.provider.order_pay.after_pay_url')
        ->parent('sylius.provider.order_pay.after_pay_url')
        ->args([
            '%sylius_shop.order_pay.after_pay_route%',
            '%sylius_shop.order_pay.after_pay_route_parameters%',
        ])
    ;

    $services
        ->set('sylius_shop.provider.order_pay.final_url')
        ->parent('sylius.provider.order_pay.final_url')
        ->args([
            '%sylius_shop.order_pay.final_route%',
            '%sylius_shop.order_pay.final_route_parameters%',
            '%sylius_shop.order_pay.retry_route%',
            '%sylius_shop.order_pay.retry_route_parameters%',
        ])
    ;

    $services
        ->set('sylius_shop.provider.order_pay.pay_response.no_payment')
        ->parent('sylius.provider.order_pay.pay_response.no_payment')
        ->args([
            service('sylius_shop.resolver.order_pay.payment_to_pay'),
            service('sylius_shop.provider.order_pay.final_url'),
        ])
        ->tag('sylius_shop.provider.order_pay.pay_response', ['priority' => -100])
    ;

    $services
        ->set('sylius_shop.provider.order_pay.pay_response.payment_request')
        ->parent('sylius.provider.order_pay.pay_response.payment_request')
        ->args([
            service('sylius_shop.resolver.order_pay.payment_to_pay'),
            service('sylius_shop.provider.order_pay.payment_request_pay_url'),
        ])
        ->tag('sylius_shop.provider.order_pay.pay_response', ['priority' => -300])
    ;

    $services
        ->set('sylius_shop.provider.order_pay.after_pay_response.payment_request')
        ->parent('sylius.provider.order_pay.after_pay_response.payment_request')
        ->args([
            service('sylius_shop.handler.order_pay.payment_state_flash'),
            service('sylius_shop.provider.order_pay.final_url'),
        ])
        ->tag('sylius_shop.provider.order_pay.after_pay_response', ['priority' => -100])
    ;
};
