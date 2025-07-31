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

use Sylius\Bundle\AdminBundle\Form\Type\TaxonType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.taxon.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(TaxonType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_taxon')")
    ->withOperations(new Operations([
        new Create(
            routeName: '_sylius_admin_taxon_create',
            redirectToRoute: 'sylius_admin_taxon_update',
        ),
        new Create(
            path: 'taxons/new/{id}',
            routeName: '_sylius_admin_taxon_create_for_parent',
            template: '@SyliusAdmin/shared/crud/create.html.twig',
            shortName: 'create_for_parent',
            factoryMethod: 'createForParent',
            factoryArguments: ['parent' => "sylius_repositories.get('sylius.repository.taxon').find(request.attributes.get('id'))"],
            notificationMessage: 'sylius.resource.create',
            redirectToRoute: 'sylius_admin_taxon_update',
        ),
        new Update(
            routeName: '_sylius_admin_taxon_update',
            redirectToRoute: 'sylius_admin_taxon_update',
        ),
        new Delete(
            routeName: '_sylius_admin_taxon_delete',
            redirectToRoute: 'sylius_admin_taxon_index',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_taxon_bulk_delete',
            redirectToRoute: 'sylius_admin_taxon_index',
        ),
    ]))
;
