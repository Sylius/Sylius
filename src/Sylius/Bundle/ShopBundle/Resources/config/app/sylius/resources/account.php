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

use Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withClass('%sylius.model.customer.class%')
    ->withSection('shop_account')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}/account')
    ->withTemplatesDir('@SyliusShop/account/')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_account')")
    ->withOperations(new Operations([
        new Show(
            path: '/dashboard',
            routeName: '_sylius_shop_account_dashboard',
            shortName: 'dashboard',
            repositoryMethod: 'find',
            repositoryArguments: [
                '@=sylius_context_shopper.getCustomer()',
            ],
        ),
        new Update(
            path: '/profile/edit',
            routeName: '_sylius_shop_account_profile_update',
            template: '@SyliusShop/account/profile_update.html.twig',
            shortName: 'profile_update',
            repositoryMethod: 'find',
            repositoryArguments: [
                '@=sylius_context_shopper.getCustomer()',
            ],
            formType: CustomerProfileType::class,
            eventShortName: 'update',
            redirectToRoute: 'sylius_shop_account_profile_update',
            redirectArguments: [],
        ),
    ]))
;
