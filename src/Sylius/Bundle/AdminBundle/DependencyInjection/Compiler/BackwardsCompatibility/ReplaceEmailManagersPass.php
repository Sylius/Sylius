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

namespace Sylius\Bundle\AdminBundle\DependencyInjection\Compiler\BackwardsCompatibility;

use Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction;
use Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction;
use Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/** @internal */
final class ReplaceEmailManagersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->getDecoratedService() === null) {
                continue;
            }

            $decoratedServiceClass = $definition->getDecoratedService()[0];

            if ($decoratedServiceClass === OrderEmailManagerInterface::class) {
                $this->replaceArgument(
                    $container,
                    ResendOrderConfirmationEmailAction::class,
                    ResendOrderConfirmationEmailAction::class,
                    1,
                    OrderEmailManagerInterface::class,
                );

                continue;
            }

            if ($decoratedServiceClass === 'sylius.email_manager.shipment') {
                $this->replaceArgument(
                    $container,
                    'sylius.listener.shipment_ship',
                    ShipmentShipListener::class,
                    0,
                    'sylius.email_manager.shipment',
                );

                continue;
            }

            if ($decoratedServiceClass === ShipmentEmailManagerInterface::class) {
                $this->replaceArgument(
                    $container,
                    ResendShipmentConfirmationEmailAction::class,
                    ResendShipmentConfirmationEmailAction::class,
                    1,
                    ShipmentEmailManagerInterface::class,
                );
            }
        }
    }

    public function replaceArgument(
        ContainerBuilder $container,
        string $serviceId,
        string $serviceClass,
        int $argumentIndex,
        string $argumentId,
    ): void {
        try {
            $listenerDefinition = $container->findDefinition($serviceId);
            if ($listenerDefinition->getClass() === $serviceClass) {
                $listenerDefinition->setArgument($argumentIndex, new Reference($argumentId));
            }
        } catch (ServiceNotFoundException) {
            return;
        }
    }
}
