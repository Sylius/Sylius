<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.custom_factory.payment_method', 'Sylius\Component\Core\Factory\PaymentMethodFactory')
        ->private()
        ->decorate('sylius.factory.payment_method')
        ->args([
            service('sylius.custom_factory.payment_method.inner'),
            service('sylius.factory.gateway_config'),
        ]);

    $services->alias('Sylius\Component\Core\Factory\PaymentMethodFactoryInterface', 'sylius.custom_factory.payment_method');
};
