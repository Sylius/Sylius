<?php

declare(strict_types=1);

use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withClass('%sylius.model.channel_pricing_log_entry.class%')
    ->withRoutePrefix('/admin')
    ->withSection('admin')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_channel_pricing_log_entry')")
    ->withOperations(new Operations([
        new Index(
            path: 'products/{productId}/variants/{variantId}/channel-pricing/{channelPricingId}/channel-pricing-log-entries',
            routeName: '_sylius_admin_channel_pricing_log_entry_index',
            template: '@SyliusAdmin/channel_pricing_log_entry/index.html.twig',
            vars: [
                'product_variant' => '@=sylius_repositories.get(\'sylius.repository.product_variant\').find(request.attributes.get(\'variantId\'))',
            ],
            grid: 'sylius_admin_channel_pricing_log_entry',
        ),
    ]))
;
