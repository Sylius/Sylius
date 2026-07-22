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

use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withClass('%sylius.model.address.class%')
    ->withSection('shop_account')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}/account/address-book')
    ->withTemplatesDir('@SyliusShop/account/address_book')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_account_address_book')")
    ->withOperations(new Operations([
        new Index(
            path: '/',
            routeName: '_sylius_shop_account_address_book_index',
            repositoryMethod: 'findByCustomer',
            repositoryArguments: [
                '@=sylius_context_shopper.getCustomer()',
            ],
        ),
        new Create(
            path: '/add',
            routeName: '_sylius_shop_account_address_book_create',
            factoryMethod: 'createForCustomer',
            factoryArguments: [
                '@=sylius_context_shopper.getCustomer()',
            ],
            notificationMessage: 'sylius.customer.add_address',
            redirectToRoute: 'sylius_shop_account_address_book_index',
            redirectArguments: [],
        ),
        new Update(
            path: '/{id}/edit',
            routeName: '_sylius_shop_account_address_book_update',
            template: '@SyliusShop/account/address_book/update.html.twig',
            repositoryMethod: 'findOneByCustomer',
            repositoryArguments: [
                '@=request.attributes.get(\'id\')',
                '@=sylius_context_shopper.getCustomer()',
            ],
            redirectToRoute: 'sylius_shop_account_address_book_index',
            redirectArguments: [],
        ),
        new Delete(
            path: '/{id}/delete',
            routeName: '_sylius_shop_account_address_book_delete',
            repositoryMethod: 'findOneByCustomer',
            repositoryArguments: [
                '@=request.attributes.get(\'id\')',
                '@=sylius_context_shopper.getCustomer()',
            ],
            redirectToRoute: 'sylius_shop_account_address_book_index',
        ),
    ]))
;
