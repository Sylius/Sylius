<?php

declare(strict_types=1);

use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;

return (new ResourceMetadata())
    ->withClass('%sylius.model.order.class%')
    ->withSection('shop_account')
    ->withRoutePrefix('/{_locale}/account/orders')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_account_order')")
    ->withOperations(new Operations([
        new Index(
            path: '/',
            routeName: '_sylius_shop_account_order_index',
            template: '@SyliusShop/account/order/index.html.twig',
            grid: 'sylius_shop_account_order',
        ),
        new Show(
            path: '/{number}',
            routeName: '_sylius_shop_account_order_show',
            template: '@SyliusShop/account/order/show.html.twig',
            repositoryMethod: 'findOneByNumberAndCustomer',
            repositoryArguments: [
                '@=request.attributes.get(\'number\')',
                '@=sylius_context_shopper.getCustomer()',
            ],
        ),
    ]))
;
