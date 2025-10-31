<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.controller.order_pay', 'Sylius\Bundle\CoreBundle\OrderPay\Controller\OrderPayController')
        ->abstract()
        ->args([
            service('sylius.repository.order'),
            inline_service('Sylius\Component\Resource\Metadata\MetadataInterface')
                ->args(['sylius.order'])
                ->factory([service('sylius.resource_registry'), 'get']),
            service('sylius.resource_controller.request_configuration_factory'),
        ]);

    $services->set('sylius.controller.payment_request_pay', 'Sylius\Bundle\CoreBundle\OrderPay\Action\PaymentRequestPayAction')
        ->abstract()
        ->args([
            inline_service('Sylius\Component\Resource\Metadata\MetadataInterface')
                ->args(['sylius.payment_request'])
                ->factory([service('sylius.resource_registry'), 'get']),
            service('sylius.resource_controller.request_configuration_factory'),
            service('sylius.repository.payment_request'),
            service('sylius.processor.payment_request.http_response'),
        ]);
};
