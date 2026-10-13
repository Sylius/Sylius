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

use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping\ResolveOrderStateListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.listener.workflow.order_shipping.resolve_order_state', ResolveOrderStateListener::class)
        ->args([service('sylius.state_resolver.order')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_shipping.completed.ship', 'priority' => 100])
    ;
};
