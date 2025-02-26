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
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.taxon.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(TaxonType::class)
    ->withOperations(new Operations([
        new Create(redirectToRoute: 'sylius_admin_taxon_update'),
        new Create(
            path: 'taxons/new/{id}',
            template: '@SyliusAdmin/shared/crud/create.html.twig',
            shortName: 'create_for_parent',
            factoryMethod: 'createForParent',
            factoryArguments: ['parent' => "sylius_repositories.get('sylius.repository.taxon').find(request.attributes.get('id'))"],
            notificationMessage: 'sylius.resource.create',
            redirectToRoute: 'sylius_admin_taxon_update',
        ),
        new Update(redirectToRoute: 'sylius_admin_taxon_update'),
        new Delete(),
        new BulkDelete(),
    ]))
;
