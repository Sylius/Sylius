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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Sylius\Bundle\CoreBundle\EventListener\CircularDependencyBreakingErrorListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CircularDependencyBreakingErrorListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('exception_listener')) {
            return;
        }

        $definition = new Definition(CircularDependencyBreakingErrorListener::class);
        $definition->setDecoratedService('exception_listener');
        $definition->addArgument(new Reference(CircularDependencyBreakingErrorListener::class . '.inner'));

        $container->setDefinition(
            CircularDependencyBreakingErrorListener::class,
            $definition,
        );
    }
}
