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

use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%/payments/{paymentId}/payment-requests')
    ->withClass('%sylius.model.payment_request.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_payment_request')")
    ->withOperations(operations: new Operations(operations: [
        new Index(
            path: '',
            routeName: '_sylius_admin_payment_request_index',
            grid: 'sylius_admin_payment_request',
        ),
        new Show(
            path: '{hash}',
            routeName: '_sylius_admin_payment_request_show',
            repositoryMethod: 'findOneByPaymentId',
            repositoryArguments: [
                'hash' => "request.attributes.get('hash')",
                'paymentId' => "request.attributes.get('paymentId')",
            ],
        ),
    ]))
;
