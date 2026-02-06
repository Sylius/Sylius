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

use Sylius\Component\Core\StateResolver\CheckoutStateResolver;
use Sylius\Component\Core\StateResolver\OrderPaymentStateResolver;
use Sylius\Component\Core\StateResolver\OrderShippingStateResolver;
use Sylius\Component\Core\StateResolver\OrderStateResolver;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.state_resolver.order', OrderStateResolver::class)
        ->args([service('sylius_abstraction.state_machine')]);

    $services->set('sylius.state_resolver.checkout', CheckoutStateResolver::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius.checker.order_payment_method_selection_requirement'),
            service('sylius.checker.order_shipping_method_selection_requirement'),
        ]);

    $services->set('sylius.state_resolver.order_payment', OrderPaymentStateResolver::class)
        ->args([service('sylius_abstraction.state_machine')]);

    $services->set('sylius.state_resolver.order_shipping', OrderShippingStateResolver::class)
        ->args([service('sylius_abstraction.state_machine')]);
};
