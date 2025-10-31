<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.provider.payment_configuration', 'Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProvider')
        ->args([tagged_iterator('sylius.api.payment_method_handler')]);

    $services->alias('sylius_api.provider.payment_configuration.composite', 'sylius_api.provider.payment_configuration');

    $services->alias('Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProviderInterface', 'sylius_api.provider.payment_configuration');
};
