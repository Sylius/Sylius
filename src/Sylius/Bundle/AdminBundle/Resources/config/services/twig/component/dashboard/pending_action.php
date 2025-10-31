<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_orders_to_process', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent')
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_orders_to_process'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_orders_to_process']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_pending_payments', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent')
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_pending_payments'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_pending_payments']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_product_reviews_to_approve', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent')
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_product_reviews_to_approve'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_product_reviews_to_approve']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_product_variants_out_of_stock', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent')
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_product_variants_out_of_stock'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_product_variants_out_of_stock']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_shipments_to_ship', 'Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent')
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_shipments_to_ship'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_shipments_to_ship']);
};
