<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.state_provider.admin.order.adjustment.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment\CollectionProvider')
        ->args([
            service('sylius.repository.order'),
            'tokenValue',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.admin.order_item.adjustment.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment\CollectionProvider')
        ->args([
            service('sylius.repository.order_item'),
            'id',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);
};
