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

use Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin/promotions/{promotionId}')
    ->withClass('%sylius.model.promotion_coupon.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(PromotionCouponType::class)
    ->withOperations(new Operations(operations: [
        new Create(
            path: 'coupons/new',
            factoryMethod: 'createForPromotion',
            factoryArguments: [
                "sylius_repositories.get('sylius.repository.promotion').find(request.attributes.get('promotionId'))",
            ],
            redirectToRoute: 'sylius_admin_promotion_coupon_index',
            redirectArguments: [
                'promotionId' => "request.attributes.get('promotionId')",
            ],
            vars: [
                'route' => [
                    'parameters' => [
                        'promotionId' => "@=request.attributes.get('promotionId')",
                    ],
                ],
            ],
        ),
        new Update(
            path: 'coupons/{id}/edit',
            repositoryMethod: 'find',
            redirectToRoute: 'sylius_admin_promotion_coupon_index',
            redirectArguments: [
                'promotionId' => "request.attributes.get('promotionId')",
            ],
            vars: [
                'route' => [
                    'parameters' => [
                        'id' => "@=request.attributes.get('id')",
                        'promotionId' => "@=request.attributes.get('promotionId')",
                    ],
                ],
            ],
        ),
        new Delete(
            path: 'coupons/{id}/delete',
            repositoryMethod: 'find',
            redirectToRoute: 'sylius_admin_promotion_coupon_index',
            redirectArguments: ['promotionId' => "request.attributes.get('promotionId')"],
        ),
        new BulkDelete(
            path: 'coupons/bulk_delete',
            repositoryArguments: ["request.request.all('ids')"],
            repositoryMethod: 'findById',
            redirectToRoute: 'sylius_admin_promotion_coupon_index',
            redirectArguments: ['promotionId' => "request.attributes.get('promotionId')"],
        ),
        new Index(
            path: 'coupons',
            vars: [
                'promotion' => "@=sylius_repositories.get('sylius.repository.promotion').find(request.attributes.get('promotionId'))",
            ],
            grid: 'sylius_admin_promotion_coupon',
        ),
    ]))
;
