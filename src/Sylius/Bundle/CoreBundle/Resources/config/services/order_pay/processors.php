<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.processor.order_pay.route_parameters', 'Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessor')
        ->args([
            inline_service(\Symfony\Component\ExpressionLanguage\ExpressionLanguage::class),
            service('router'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessorInterface', 'sylius.processor.order_pay.route_parameters');
};
