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

use Sylius\Bundle\AdminBundle\Form\Type\TaxCategoryType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.tax_category.class%')
    ->withSection('admin')
    ->withFormType(TaxCategoryType::class)
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_tax_category')")
    ->withOperations(operations: new Operations(operations: [
        new Create(
            routeName: '_sylius_admin_tax_category_create',
            redirectToRoute: 'sylius_admin_tax_category_update',
        ),
        new Update(
            routeName: '_sylius_admin_tax_category_update',
            redirectToRoute: 'sylius_admin_tax_category_update',
        ),
        new Delete(
            routeName: '_sylius_admin_tax_category_delete',
            redirectToRoute: 'sylius_admin_tax_category_index',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_tax_category_bulk_delete',
            redirectToRoute: 'sylius_admin_tax_category_index',
        ),
        new Index(
            routeName: '_sylius_admin_tax_category_index',
            grid: 'sylius_admin_tax_category',
        ),
    ]))
;
