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

use Sylius\Bundle\ShopBundle\Form\Type\CustomerDefaultAddressType;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withClass('%sylius.model.customer.class%')
    ->withSection('shop_account')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}/account/address-book')
    ->withFormType(CustomerDefaultAddressType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_account_address_book')")
    ->withOperations(new Operations([
        new Update(
            methods: [
                'GET',
                'POST',
                'PATCH',
            ],
            path: '/{id}/set-as-default',
            routeName: '_sylius_shop_account_address_book_set_as_default',
            shortName: 'set_as_default',
            repositoryMethod: 'find',
            repositoryArguments: [
                '@=sylius_context_shopper.getCustomer()',
            ],
            formOptions: [
                'customer' => '@=sylius_context_shopper.getCustomer()',
            ],
            notificationMessage: 'sylius.customer.set_address_as_default',
            redirectToRoute: 'sylius_shop_account_address_book_index',
            redirectArguments: [],
        ),
    ]))
;
