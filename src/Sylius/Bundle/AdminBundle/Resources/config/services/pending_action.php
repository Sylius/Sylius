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

use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountOrdersToProcessProvider;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountPendingPaymentsProvider;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountProductReviewsToApproveProvider;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountProductVariantsOutOfStockProvider;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountShipmentsToShipProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.provider.pending_action.count_orders_to_process', CountOrdersToProcessProvider::class)
        ->args([service('sylius.repository.order')]);

    $services->set('sylius_admin.provider.pending_action.count_pending_payments', CountPendingPaymentsProvider::class)
        ->args([service('sylius.repository.payment')]);

    $services->set('sylius_admin.provider.pending_action.count_product_reviews_to_approve', CountProductReviewsToApproveProvider::class)
        ->args([service('sylius.repository.product_review')]);

    $services->set('sylius_admin.provider.pending_action.count_product_variants_out_of_stock', CountProductVariantsOutOfStockProvider::class)
        ->args([service('sylius.repository.product_variant')]);

    $services->set('sylius_admin.provider.pending_action.count_shipments_to_ship', CountShipmentsToShipProvider::class)
        ->args([service('sylius.repository.shipment')]);
};
