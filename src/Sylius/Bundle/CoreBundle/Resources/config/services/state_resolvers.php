<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.state_resolver.order', 'Sylius\Component\Core\StateResolver\OrderStateResolver')
        ->args([service('sylius_abstraction.state_machine')]);

    $services->set('sylius.state_resolver.checkout', 'Sylius\Component\Core\StateResolver\CheckoutStateResolver')
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius.checker.order_payment_method_selection_requirement'),
            service('sylius.checker.order_shipping_method_selection_requirement'),
        ]);

    $services->set('sylius.state_resolver.order_payment', 'Sylius\Component\Core\StateResolver\OrderPaymentStateResolver')
        ->args([service('sylius_abstraction.state_machine')]);

    $services->set('sylius.state_resolver.order_shipping', 'Sylius\Component\Core\StateResolver\OrderShippingStateResolver')
        ->args([service('sylius_abstraction.state_machine')]);
};
