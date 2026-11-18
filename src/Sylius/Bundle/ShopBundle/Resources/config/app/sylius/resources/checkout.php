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

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\AddressType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\SelectPaymentType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\SelectShippingType;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withClass('%sylius.model.order.class%')
    ->withSection('shop')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}/checkout')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_checkout')")
    ->withOperations(new Operations([
        new Update(
            path: '/address',
            routeName: '_sylius_shop_checkout_address',
            template: '@SyliusShop/checkout/address.html.twig',
            shortName: 'checkout_address',
            repositoryMethod: 'findCartForAddressing',
            repositoryArguments: [
                '@=sylius_context_cart.getCart().getId()',
            ],
            formType: AddressType::class,
            formOptions: [
                'customer' => '@=sylius_context_shopper.getCustomer()',
            ],
            eventShortName: 'address',
            stateMachineTransition: 'address',
            stateMachineGraph: 'sylius_order_checkout',
        ),
        new Update(
            path: '/select-shipping',
            routeName: '_sylius_shop_checkout_select_shipping',
            template: '@SyliusShop/checkout/select_shipping.html.twig',
            shortName: 'checkout_select_shipping',
            repositoryMethod: 'findCartForSelectingShipping',
            repositoryArguments: [
                '@=sylius_context_cart.getCart().getId()',
            ],
            formType: SelectShippingType::class,
            eventShortName: 'select_shipping',
            stateMachineTransition: 'select_shipping',
            stateMachineGraph: 'sylius_order_checkout',
        ),
        new Update(
            path: '/select-payment',
            routeName: '_sylius_shop_checkout_select_payment',
            template: '@SyliusShop/checkout/select_payment.html.twig',
            shortName: 'checkout_select_payment',
            repositoryMethod: 'findCartForSelectingPayment',
            repositoryArguments: [
                '@=sylius_context_cart.getCart().getId()',
            ],
            formType: SelectPaymentType::class,
            eventShortName: 'payment',
            stateMachineTransition: 'select_payment',
            stateMachineGraph: 'sylius_order_checkout',
        ),
        new Update(
            path: '/complete',
            routeName: '_sylius_shop_checkout_complete',
            template: '@SyliusShop/checkout/complete.html.twig',
            shortName: 'checkout_complete',
            repositoryMethod: 'findCartForSummary',
            repositoryArguments: [
                '@=sylius_context_cart.getCart().getId()',
            ],
            formType: CompleteType::class,
            eventShortName: 'complete',
            redirectToRoute: 'sylius_shop_order_pay',
            redirectArguments: [
                'tokenValue' => 'resource.tokenValue',
            ],
            stateMachineTransition: 'complete',
            stateMachineGraph: 'sylius_order_checkout',
        ),
    ]))
;
