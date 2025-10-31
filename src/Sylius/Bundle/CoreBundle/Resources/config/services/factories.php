<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.custom_factory.order', 'Sylius\Bundle\CoreBundle\Factory\OrderFactory')
        ->decorate('sylius.factory.order')
        ->args([service('sylius.custom_factory.order.inner')]);

    $services->alias('Sylius\Bundle\CoreBundle\Factory\OrderFactoryInterface', 'sylius.custom_factory.order');
};
