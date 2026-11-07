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

use Sylius\Bundle\AdminBundle\Form\Type\ExchangeRateType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.exchange_rate.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(ExchangeRateType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_exchange_rate')")
    ->withOperations(new Operations([
        new Create(
            routeName: '_sylius_admin_exchange_rate_create',
            redirectToRoute: 'sylius_admin_exchange_rate_update',
        ),
        new Update(
            routeName: '_sylius_admin_exchange_rate_update',
            redirectToRoute: 'sylius_admin_exchange_rate_update',
        ),
        new Delete(
            routeName: '_sylius_admin_exchange_rate_delete',
            redirectToRoute: 'sylius_admin_exchange_rate_index',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_exchange_rate_bulk_delete',
            redirectToRoute: 'sylius_admin_exchange_rate_index',
        ),
        new Index(
            routeName: '_sylius_admin_exchange_rate_index',
            grid: 'sylius_admin_exchange_rate',
        )
    ]))
;
