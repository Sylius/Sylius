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

use Sylius\Bundle\AdminBundle\Form\Type\OrderType;
use Sylius\Bundle\CoreBundle\StateMachine\State\ApplyStateMachineTransitionProcessor;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.order.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(OrderType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_order')")
    ->withOperations(new Operations(operations: [
        new Index(
            routeName: '_sylius_admin_order_index',
            grid: 'sylius_admin_order',
        ),
        new Show(
            routeName: '_sylius_admin_order_show',
            repositoryMethod: 'findOrderById',
        ),
        new Show(
            routeName: '_sylius_admin_order_history',
            template: '@SyliusAdmin/order/history.html.twig',
            shortName: 'history',
            repositoryMethod: 'findOrderById',
        ),
        new Update(
            routeName: '_sylius_admin_order_update',
            repositoryMethod: 'findOrderById',
            formOptions: ['validation_groups' => ['sylius_shipping_address_update']],
            redirectToRoute: 'sylius_admin_order_show',
        ),
        new ApplyStateMachineTransition(
            routeName: '_sylius_admin_order_cancel',
            processor: ApplyStateMachineTransitionProcessor::class,
            repositoryMethod: 'findOrderById',
            notificationMessage: 'sylius.resource.update',
            redirectToRoute: 'sylius_admin_order_show',
            stateMachineTransition: 'cancel',
            stateMachineGraph: 'sylius_order',
        ),
    ]))
;
