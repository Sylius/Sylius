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

use Sylius\Bundle\AdminBundle\Form\Type\ProductReviewType;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.product_review.class%')
    ->withFormType(ProductReviewType::class)
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withOperations(operations: new Operations(operations: [
        new Update(redirectToRoute: 'sylius_admin_product_review_update'),
        new Delete(),
        new BulkDelete(),
        new Index(grid: 'sylius_admin_product_review'),
        new ApplyStateMachineTransition(
            notificationMessage: 'sylius.review.accept',
            redirectToRoute: 'sylius_admin_product_review_index',
            stateMachineTransition: 'accept',
            stateMachineGraph: 'sylius_product_review',
        ),
        new ApplyStateMachineTransition(
            notificationMessage: 'sylius.review.reject',
            redirectToRoute: 'sylius_admin_product_review_index',
            stateMachineTransition: 'reject',
            stateMachineGraph: 'sylius_product_review',
        ),
    ]))
;
