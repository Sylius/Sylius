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
use Sylius\Bundle\CoreBundle\StateMachine\State\ApplyStateMachineTransitionProcessor;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.product_review.class%')
    ->withFormType(ProductReviewType::class)
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_product_review')")
    ->withOperations(operations: new Operations(operations: [
        new Update(
            routeName: '_sylius_admin_product_review_update',
            redirectToRoute: 'sylius_admin_product_review_update',
        ),
        new Delete(
            routeName: '_sylius_admin_product_review_delete',
            redirectToRoute: 'sylius_admin_product_review_index',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_product_review_bulk_delete',
            redirectToRoute: 'sylius_admin_product_review_index',
        ),
        new Index(
            routeName: '_sylius_admin_product_review_index',
            grid: 'sylius_admin_product_review',
        ),
        new ApplyStateMachineTransition(
            routeName: '_sylius_admin_product_review_accept',
            processor: ApplyStateMachineTransitionProcessor::class,
            notificationMessage: 'sylius.review.accept',
            redirectToRoute: 'sylius_admin_product_review_index',
            stateMachineTransition: 'accept',
            stateMachineGraph: 'sylius_product_review',
        ),
        new ApplyStateMachineTransition(
            routeName: '_sylius_admin_product_review_reject',
            processor: ApplyStateMachineTransitionProcessor::class,
            notificationMessage: 'sylius.review.reject',
            redirectToRoute: 'sylius_admin_product_review_index',
            stateMachineTransition: 'reject',
            stateMachineGraph: 'sylius_product_review',
        ),
    ]))
;
