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
        ->set('sylius_shop.provider.order_pay.pay_response.payum')
        ->parent('sylius_payum.provider.order_pay.pay_response')
        ->args([service('sylius_shop.resolver.order_pay.payment_to_pay')])
        ->tag('sylius_shop.provider.order_pay.pay_response', ['priority' => -200])
    ;

    $services
        ->set('sylius_shop.provider.order_pay.after_pay_response.payum')
        ->parent('sylius_payum.provider.order_pay.after_pay_response')
        ->args([service('sylius_shop.handler.order_pay.payment_state_flash')])
        ->tag('sylius_shop.provider.order_pay.after_pay_response', ['priority' => -200])
    ;
};
