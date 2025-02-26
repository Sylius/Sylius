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

use Sylius\Bundle\AdminBundle\Form\Type\ProductGenerateVariantsType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.product.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(ProductType::class)
    ->withOperations(new Operations([
        new Create(redirectToRoute: 'sylius_admin_product_update'),
//        new Create(
//            path: 'products/new/simple',
//            template: '@SyliusAdmin/shared/crud/create.html.twig',
//            shortName: 'create_simple',
//            factoryMethod: 'createWithVariant',
//            notificationMessage: 'sylius.resource.create',
//            redirectToRoute: 'sylius_admin_product_update',
//        ),
        new Update(redirectToRoute: 'sylius_admin_product_update'),
        new Delete(),
        new BulkDelete(),
        new BulkDelete(
            path: 'taxons/{taxonId}/products/bulk-delete',
            routeName: 'sylius_admin_product_taxon_bulk_delete_products',
            shortName: 'product_taxon_bulk_delete_products',
            repositoryArguments: ["request.request.all('ids')"],
            repositoryMethod: 'findByProductTaxonIds',
        ),
        new Index(grid: 'sylius_admin_product'),
        new Show(),
        new Update(
            methods: ['GET', 'POST'],
            path: 'products/{productId}/variants/generate',
            routeName: 'sylius_admin_product_variant_generate',
            template: '@SyliusAdmin/product/generate_variants.html.twig',
            shortName: 'variants_generate',
            repositoryMethod: 'find',
            repositoryArguments: ["request.attributes.get('productId')"],
            formType: ProductGenerateVariantsType::class,
            notificationMessage: 'sylius.product_variant.generate',
            redirectToRoute: 'sylius_admin_product_variant_index',
            redirectArguments: ['productId' => "request.attributes.get('productId')"],
        ),
    ]))
;
