<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Payum\Bundle\PayumBundle\Controller;
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
        Controller\AuthorizeController::class,
        Controller\CancelController::class,
        Controller\CaptureController::class,
        Controller\NotifyController::class,
        Controller\PayoutController::class,
        Controller\RefundController::class,
        Controller\SyncController::class,
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::SERVICES as $service) {
            try {
                $definition = $container->findDefinition($service);
            } catch (ServiceNotFoundException $exception) {
                $definition = new Definition($service);

                $container->setDefinition($service, $definition);
            }

            $definition->addMethodCall('setContainer', [new Reference('service_container')]);
            $definition->setPublic(true);
        }
    }
}
