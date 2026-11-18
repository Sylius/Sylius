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

use Sylius\Bundle\CoreBundle\StateMachine\State\ApplyStateMachineTransitionProcessor;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.payment.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_payment')")
    ->withOperations(operations: new Operations(operations: [
        new Index(
            routeName: '_sylius_admin_payment_index',
            grid: 'sylius_admin_payment',
        ),
        new ApplyStateMachineTransition(
            routeName: '_sylius_admin_payment_complete',
            processor: ApplyStateMachineTransitionProcessor::class,
            eventShortName: 'complete',
            notificationMessage: 'sylius.payment.completed',
            redirectToRoute: 'sylius_admin_payment_index',
            stateMachineTransition: 'complete',
            stateMachineGraph: 'sylius_payment',
        ),
    ]))
;
