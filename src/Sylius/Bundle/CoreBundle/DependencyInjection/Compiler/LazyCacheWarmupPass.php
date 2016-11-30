<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LazyCacheWarmupPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->markServiceAsLazy($container, 'cmf_core.templating.helper');
        $this->markServiceAsLazy($container, 'cmf_create.rdf_type_factory');

        if ($container->has('fos_oauth_server.server')) {
            $this->markServiceAsLazy($container, 'fos_oauth_server.server');
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $id
     */
    private function markServiceAsLazy(ContainerBuilder $container, $id)
    {
        try {
            $definition = $container->findDefinition($id);
            $definition->setLazy(true);
        } catch (InvalidArgumentException $exception) {
            // intentionally left blank
        }
    }
}
