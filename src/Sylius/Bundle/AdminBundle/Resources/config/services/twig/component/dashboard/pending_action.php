<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_orders_to_process', PendingActionCountComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_orders_to_process'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_orders_to_process']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_pending_payments', PendingActionCountComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_pending_payments'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_pending_payments']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_product_reviews_to_approve', PendingActionCountComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_product_reviews_to_approve'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_product_reviews_to_approve']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_product_variants_out_of_stock', PendingActionCountComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_product_variants_out_of_stock'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_product_variants_out_of_stock']);

    $services->set('sylius_admin.twig.component.dashboard.pending_action.count_shipments_to_ship', PendingActionCountComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius_admin.provider.pending_action.count_shipments_to_ship'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:pending_action:count_shipments_to_ship']);
};
