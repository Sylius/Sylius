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

use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.catalog_promotion.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(CatalogPromotionType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_catalog_promotion')")
    ->withOperations(new Operations(operations: [
        new Create(
            routeName: '_sylius_admin_catalog_promotion_create',
            redirectToRoute: 'sylius_admin_catalog_promotion_update',
        ),
        new Update(
            routeName: '_sylius_admin_catalog_promotion_update',
            redirectToRoute: 'sylius_admin_catalog_promotion_update',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_catalog_promotion_bulk_delete',
            redirectToRoute: 'sylius_admin_catalog_promotion_index',
        ),
        new Index(
            routeName: '_sylius_admin_catalog_promotion_index',
            grid: 'sylius_admin_catalog_promotion',
        ),
        new Show(
            routeName: '_sylius_admin_catalog_promotion_show',
        ),
    ]))
;
