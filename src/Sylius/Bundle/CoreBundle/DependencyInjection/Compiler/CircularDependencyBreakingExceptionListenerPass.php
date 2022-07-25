<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Sylius\Bundle\CoreBundle\EventListener\CircularDependencyBreakingExceptionListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CircularDependencyBreakingExceptionListenerPass implements CompilerPassInterface
{
    /** @psalm-suppress MissingDependency */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('twig.exception_listener')) {
            return;
        }

        $definition = new Definition(CircularDependencyBreakingExceptionListener::class);
        $definition->setDecoratedService('twig.exception_listener');
        $definition->addArgument(new Reference(CircularDependencyBreakingExceptionListener::class . '.inner'));

        $container->setDefinition(
            CircularDependencyBreakingExceptionListener::class,
            $definition,
        );
    }
}
