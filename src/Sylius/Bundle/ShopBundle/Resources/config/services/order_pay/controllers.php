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
        ->set('sylius_shop.controller.order_pay')
        ->parent('sylius.controller.order_pay')
        ->args([
            tagged_iterator('sylius_shop.provider.order_pay.pay_response'),
            tagged_iterator('sylius_shop.provider.order_pay.after_pay_response'),
        ])
        ->public()
    ;

    $services
        ->set('sylius_shop.controller.payment_request_pay')
        ->parent('sylius.controller.payment_request_pay')
        ->args([service('sylius_shop.provider.order_pay.after_pay_url')])
        ->public()
        ->tag('controller.service_arguments')
    ;
};
