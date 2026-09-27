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

use Sylius\Bundle\PayumBundle\OrderPay\Provider\PayumAfterPayResponseProvider;
use Sylius\Bundle\PayumBundle\OrderPay\Provider\PayumPayResponseProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_payum.provider.order_pay.pay_response', PayumPayResponseProvider::class)
        ->args([service('payum')])
        ->abstract()
    ;

    $services
        ->set('sylius_payum.provider.order_pay.after_pay_response', PayumAfterPayResponseProvider::class)
        ->args([
            service('payum'),
            service('router'),
            service('sylius_payum.factory.get_status'),
            service('sylius_payum.factory.resolve_next_route'),
        ])
        ->abstract()
    ;
};
