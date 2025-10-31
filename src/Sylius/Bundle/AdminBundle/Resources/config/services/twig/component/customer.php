<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.customer.order_statistics', 'Sylius\Bundle\AdminBundle\Twig\Component\Customer\OrderStatisticsComponent')
        ->args([
            service('sylius.repository.customer'),
            service('sylius.provider.statistics.customer'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:customer:order_statistics']);
};
