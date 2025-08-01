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

use Sylius\Bundle\ShopBundle\Form\Type\Product\ProductReviewType;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withRoutePrefix('/{_locale}/products/{slug}/reviews')
    ->withClass('%sylius.model.product_review.class%')
    ->withFormType(ProductReviewType::class)
    ->withSection('shop')
    ->withTemplatesDir('@SyliusShop/product_review')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_product_review')")
    ->withOperations(operations: new Operations(operations: [
        new Index(
            path: '/',
            routeName: '_sylius_shop_product_review_index',
            repositoryMethod: 'findAcceptedByProductSlugAndChannel',
            repositoryArguments: [
                'slug' => "@=request.attributes.get('slug')",
                'locale' => '@=sylius_context_shopper.getLocaleCode()',
                'channel' => '@=sylius_context_shopper.getChannel()',
            ],
        ),
        new Create(
            path: '/new',
            routeName: '_sylius_shop_product_review_create',
            factoryMethod: 'createForSubjectWithReviewer',
            factoryArguments: [
                'subject' => "@=throw_not_found_on_null(sylius_repositories.get('sylius.repository.product').findOneByChannelAndSlug(sylius_context_shopper.getChannel(), sylius_context_shopper.getLocaleCode(), request.attributes.get('slug')))",
                'reviewer' => '@=sylius_context_shopper.getCustomer()',
            ],
            formOptions: [
                'validation_groups' => ['sylius', 'sylius_review'],
            ],
            notificationMessage: 'sylius.review.wait_for_the_acceptation',
            redirectToRoute: 'sylius_shop_product_show',
            redirectArguments: [
                'slug' => "@=request.attributes.get('slug')",
            ],
        ),
    ]))
;
