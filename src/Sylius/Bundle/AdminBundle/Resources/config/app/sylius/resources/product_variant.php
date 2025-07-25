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

use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%/products/{productId}')
    ->withClass('%sylius.model.product_variant.class%')
    ->withFormType(ProductVariantType::class)
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_product_variant')")
    ->withOperations(operations: new Operations(operations: [
        new Index(
            path: 'variants/',
            routeName: '_sylius_admin_product_variant_index',
            vars: [
                'product' => "@=sylius_repositories.get('sylius.repository.product').find(request.attributes.get('productId'))",
            ],
            grid: 'sylius_admin_product_variant',
        ),
        new Create(
            path: 'variants/new',
            routeName: '_sylius_admin_product_variant_create',
            factoryMethod: 'createForProduct',
            factoryArguments: [
                "throw_not_found_on_null(sylius_repositories.get('sylius.repository.product').find(request.attributes.get('productId')))"
            ],
            redirectToRoute: 'sylius_admin_product_variant_index',
            redirectArguments: ['productId' => "request.attributes.get('productId')"],
            vars: [
                'route' => [
                    'parameters' => [
                        'id' => "@=request.attributes.get('id')",
                        'productId' => "@=request.attributes.get('productId')",
                    ],
                ],
            ],
        ),
        new Update(
            path: 'variants/{id}/edit',
            routeName: '_sylius_admin_product_variant_update',
            repositoryMethod: 'findOneByIdAndProductId',
            redirectToRoute: 'sylius_admin_product_variant_index',
            redirectArguments: ['productId' => "request.attributes.get('productId')"],
            vars: [
                'route' => [
                    'parameters' => [
                        'id' => "@=request.attributes.get('id')",
                        'productId' => "@=request.attributes.get('productId')",
                    ],
                ],
            ],
        ),
        new BulkDelete(
            path: 'variants/bulk-delete',
            routeName: '_sylius_admin_product_variant_bulk_delete',
            repositoryArguments: [
                "request.request.all('ids')",
            ],
            repositoryMethod: 'findById',
            redirectToRoute: 'sylius_admin_product_variant_index',
            redirectArguments: ['productId' => "request.attributes.get('productId')"],
            vars: [
                'route' => [
                    'parameters' => [
                        'id' => "@=request.attributes.get('id')",
                        'productId' => "@=request.attributes.get('productId')",
                    ],
                ],
            ],
        ),
        new Delete(
            path: 'variants/{id}/delete',
            routeName: '_sylius_admin_product_variant_delete',
            repositoryMethod: 'findOneByIdAndProductId',
            redirectToRoute: 'sylius_admin_product_variant_index',
            redirectArguments: ['productId' => "request.attributes.get('productId')"],
            vars: [
                'route' => [
                    'parameters' => [
                        'id' => "@=request.attributes.get('id')",
                        'productId' => "@=request.attributes.get('productId')",
                    ],
                ],
            ],
        ),
    ]))
;
