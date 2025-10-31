<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.workflow.order_shipping.resolve_order_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping\ResolveOrderStateListener')
        ->args([service('sylius.state_resolver.order')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_shipping.completed.ship', 'priority' => 100]);
};
