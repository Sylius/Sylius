<?php

declare(strict_types=1);

use Sylius\Bundle\ShopBundle\Form\Type\CustomerRegistrationType;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withClass('%sylius.model.customer.class%')
    ->withSection('shop')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}')
    ->withFormType(CustomerRegistrationType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_register')")
    ->withOperations(new Operations([
        new Create(
            path: '/register',
            routeName: '_sylius_shop_register',
            template: '@SyliusShop/account/register.html.twig',
            shortName: 'register',
            eventShortName: 'register',
            notificationMessage: 'sylius.customer.register',
            redirectToRoute: 'sylius_shop_register_thank_you',
        ),
        new Create(
            methods: [
                'GET',
            ],
            path: '/register-after-checkout/{tokenValue}',
            routeName: '_sylius_shop_register_after_checkout',
            template: '@SyliusShop/account/register.html.twig',
            shortName: 'register_after_checkout',
            factory: 'sylius.factory.customer_after_checkout',
            factoryMethod: 'createAfterCheckout',
            factoryArguments: [
                '@=sylius_repositories.get(\'sylius.repository.order\').findOneByTokenValue(request.attributes.get(\'tokenValue\'))',
            ],
            eventShortName: 'register',
            notificationMessage: 'sylius.customer.register',
            redirectToRoute: 'sylius_shop_register_thank_you',
        ),
    ]))
;
