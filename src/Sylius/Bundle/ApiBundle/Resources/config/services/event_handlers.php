<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.event_handler.order_completed', 'Sylius\Bundle\ApiBundle\EventHandler\OrderCompletedHandler')
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.order'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);
};
