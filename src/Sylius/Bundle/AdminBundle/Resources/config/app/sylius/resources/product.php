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
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.product.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(ProductType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_product')")
    ->withOperations(new Operations([
        new Create(routeName: '_sylius_admin_product_create', redirectToRoute: 'sylius_admin_product_update'),
        new Create(
            path: 'products/new/simple',
            routeName: '_sylius_admin_product_create_simple',
            template: '@SyliusAdmin/shared/crud/create.html.twig',
            shortName: 'create_simple',
            factoryMethod: 'createWithVariant',
            eventShortName: 'create',
            notificationMessage: 'sylius.resource.create',
            redirectToRoute: 'sylius_admin_product_update',
            vars: [
                'route' => [
                    'name' => 'sylius_admin_product_create_simple',
                ],
            ],
        ),
        new Update(routeName: '_sylius_admin_product_update', redirectToRoute: 'sylius_admin_product_update'),
        new Update(
            methods: ['GET', 'POST'],
            path: 'products/{productId}/variants/generate',
            routeName: '_sylius_admin_product_variant_generate',
            template: '@SyliusAdmin/product/generate_variants.html.twig',
            shortName: 'generate_variants',
            repositoryMethod: 'find',
            repositoryArguments: ["@=request.attributes.get('productId')"],
            formType: ProductGenerateVariantsType::class,
            eventShortName: 'update',
            notificationMessage: 'sylius.product_variant.generate',
            redirectToRoute: 'sylius_admin_product_variant_index',
            redirectArguments: ['productId' => "@=request.attributes.get('productId')"],
        ),
        new Delete(routeName: '_sylius_admin_product_delete', redirectToRoute: 'sylius_admin_product_index'),
        new BulkDelete(routeName: '_sylius_admin_product_bulk_delete', redirectToRoute: 'sylius_admin_product_index'),
        new BulkDelete(
            path: 'taxons/{taxonId}/products/bulk-delete',
            routeName: '_sylius_admin_product_taxon_bulk_delete_products',
            shortName: 'product_taxon_bulk_delete_products',
            repositoryArguments: [
                '@=request.request.all(\'ids\')',
            ],
            repositoryMethod: 'findByProductTaxonIds',
            redirectTo: 'referer',
        ),
        new Index(routeName: '_sylius_admin_product_index', grid: 'sylius_admin_product'),
        new Show(routeName: '_sylius_admin_product_show'),
    ]))
;
