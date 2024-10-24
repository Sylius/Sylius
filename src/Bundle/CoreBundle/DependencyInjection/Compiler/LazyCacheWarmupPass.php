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
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class LazyCacheWarmupPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->markServiceAsLazy($container, 'cmf_core.templating.helper');
        $this->markServiceAsLazy($container, 'cmf_create.rdf_type_factory');

        if ($container->has('fos_oauth_server.server')) {
            $this->markServiceAsLazy($container, 'fos_oauth_server.server');
        }
    }

    private function markServiceAsLazy(ContainerBuilder $container, string $id): void
    {
        try {
            $definition = $container->findDefinition($id);
            $definition->setLazy(true);
        } catch (InvalidArgumentException) {
            // intentionally left blank
        }
    }
}
