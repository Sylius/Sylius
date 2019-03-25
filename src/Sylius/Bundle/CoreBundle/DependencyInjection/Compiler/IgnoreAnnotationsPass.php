<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class IgnoreAnnotationsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $annotationsReader = $container->getDefinition('annotations.reader');

        $annotationsReader->addMethodCall('addGlobalIgnoredName', ['template']);
        $annotationsReader->addMethodCall('addGlobalIgnoredName', ['psalm']);
    }
}
