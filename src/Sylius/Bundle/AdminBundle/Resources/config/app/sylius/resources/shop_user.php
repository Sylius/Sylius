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

use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.shop_user.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withOperations(new Operations(operations: [
        new Delete(
            redirectToRoute: 'sylius_admin_customer_show',
            redirectArguments: ['id' => "request.query.get('customerId')"],
        )
    ]))
;
