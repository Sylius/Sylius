<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.dashboard.channel_selector', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\ChannelSelectorComponent')
        ->args([service('sylius.repository.channel')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:channel_selector']);

    $services->set('sylius_admin.twig.component.dashboard.new_customers', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\NewCustomersComponent')
        ->args([service('sylius.repository.customer')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:dashboard:new_customers']);

    $services->set('sylius_admin.twig.component.dashboard.new_orders', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\NewOrdersComponent')
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.channel'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:new_orders']);

    $services->set('sylius_admin.twig.component.dashboard.statistics', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\StatisticsComponent')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.provider.statistics'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:statistics']);
};
