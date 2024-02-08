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
    }
}
