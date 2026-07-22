<?php

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withClass('%sylius.model.order.class%')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}/order')
    ->withTemplatesDir('@SyliusShop/order')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_order')")
    ->withOperations(new Operations([
        new Update(
            path: '/{tokenValue}',
            routeName: '_sylius_shop_order_show',
            routePriority: -1, // should be after "sylius_shop_order_pay" route
            shortName: 'show',
            repositoryMethod: 'findOneBy',
            repositoryArguments: [
                [
                    'tokenValue' => "@=request.attributes.get('tokenValue')",
                ],
            ],
            formType: SelectPaymentType::class,
            formOptions: [
                'validation_groups' => ['sylius_order_pay'],
            ],
            eventShortName: 'update',
            redirectToRoute: 'sylius_shop_order_pay',
            redirectArguments: [
                'tokenValue' => 'resource.tokenValue',
            ],
        ),
    ]))
;
