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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ProcessOrderListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ResolveOrderPaymentStateListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.listener.workflow.payment.process_order', ProcessOrderListener::class)
        ->args([service('sylius.order_processing.order_payment_processor.after_checkout')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.fail', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.cancel', 'priority' => 100])
    ;

    $services
        ->set('sylius.listener.workflow.payment.resolve_order_payment_state', ResolveOrderPaymentStateListener::class)
        ->args([service('sylius.state_resolver.order_payment')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.complete', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.process', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.refund', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.authorize', 'priority' => 100])
    ;
};
