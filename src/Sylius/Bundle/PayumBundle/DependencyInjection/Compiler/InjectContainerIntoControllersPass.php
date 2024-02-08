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

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Payum\Bundle\PayumBundle\Controller\AuthorizeController;
use Payum\Bundle\PayumBundle\Controller\CancelController;
use Payum\Bundle\PayumBundle\Controller\CaptureController;
use Payum\Bundle\PayumBundle\Controller\NotifyController;
use Payum\Bundle\PayumBundle\Controller\PayoutController;
use Payum\Bundle\PayumBundle\Controller\RefundController;
use Payum\Bundle\PayumBundle\Controller\SyncController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @see https://github.com/Payum/PayumBundle/issues/507
 *
 * @internal
 */
final class InjectContainerIntoControllersPass implements CompilerPassInterface
{
    private const SERVICES = [
        AuthorizeController::class,
        CancelController::class,
        CaptureController::class,
        NotifyController::class,
        PayoutController::class,
        RefundController::class,
        SyncController::class,
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::SERVICES as $service) {
            try {
                $definition = $container->findDefinition($service);
            } catch (ServiceNotFoundException) {
                $definition = new Definition($service);

                $container->setDefinition($service, $definition);
            }

            $definition->addMethodCall('setContainer', [new Reference('service_container')]);
            $definition->setPublic(true);
        }
    }
}
