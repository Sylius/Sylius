<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.provider.pending_action.count_orders_to_process', 'Sylius\Bundle\AdminBundle\PendingAction\Provider\CountOrdersToProcessProvider')
        ->args([service('sylius.repository.order')]);

    $services->set('sylius_admin.provider.pending_action.count_pending_payments', 'Sylius\Bundle\AdminBundle\PendingAction\Provider\CountPendingPaymentsProvider')
        ->args([service('sylius.repository.payment')]);

    $services->set('sylius_admin.provider.pending_action.count_product_reviews_to_approve', 'Sylius\Bundle\AdminBundle\PendingAction\Provider\CountProductReviewsToApproveProvider')
        ->args([service('sylius.repository.product_review')]);

    $services->set('sylius_admin.provider.pending_action.count_product_variants_out_of_stock', 'Sylius\Bundle\AdminBundle\PendingAction\Provider\CountProductVariantsOutOfStockProvider')
        ->args([service('sylius.repository.product_variant')]);

    $services->set('sylius_admin.provider.pending_action.count_shipments_to_ship', 'Sylius\Bundle\AdminBundle\PendingAction\Provider\CountShipmentsToShipProvider')
        ->args([service('sylius.repository.shipment')]);
};
