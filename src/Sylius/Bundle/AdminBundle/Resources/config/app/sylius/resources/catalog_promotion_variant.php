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
    ->withRoutePrefix('/admin/catalog-promotions/{id}')
    ->withClass('%sylius.model.product_variant.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withOperations(new Operations(operations: [
        new Index(
            routeName: 'sylius_admin_catalog_promotion_product_variant_index',
            template: '@SyliusAdmin/catalog_promotion/product_variant/index.html.twig',
            vars: [
                'catalogPromotion' => "@=sylius_repositories.get('sylius.repository.catalog_promotion').find(request.attributes.get('id'))",
            ],
            grid: 'sylius_admin_product_variant_with_catalog_promotion',
        ),
    ]))
;
