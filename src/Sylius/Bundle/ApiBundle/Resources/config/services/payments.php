<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.controller.payment.get_payment_configuration', 'Sylius\Bundle\ApiBundle\Controller\Payment\GetPaymentConfiguration')
        ->args([
            service('sylius.repository.payment'),
            service('sylius_api.provider.payment_configuration'),
        ]);
};
