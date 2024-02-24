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

namespace Sylius\Bundle\ShopBundle\DependencyInjection\Compiler\BackwardsCompatibility;

use Sylius\Bundle\ShopBundle\Controller\ContactController;
use Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

            $decoratedServiceId = $definition->getDecoratedService()[0];

            if ($decoratedServiceId === 'sylius.email_manager.contact') {
                $this->replaceArgument(
                    $container,
                    'sylius.controller.shop.contact',
                    ContactController::class,
                    6,
                    'sylius.email_manager.contact',
                );

                continue;
            }

            if ($decoratedServiceId === 'sylius.email_manager.order') {
                $this->replaceArgument(
                    $container,
                    'sylius.listener.order_complete',
                    OrderCompleteListener::class,
                    0,
                    'sylius.email_manager.order',
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
        if (!$container->hasDefinition($serviceId)) {
            return;
        }

        $definition = $container->findDefinition($serviceId);
        if ($definition->getClass() === $serviceClass) {
            $definition->setArgument($argumentIndex, new Reference($argumentId));
        }
    }
}
