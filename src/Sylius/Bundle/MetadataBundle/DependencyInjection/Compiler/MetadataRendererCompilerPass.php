<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataRendererCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.metadata.renderer')) {
            return;
        }

        $definition = $container->getDefinition('sylius.metadata.renderer');

        $taggedServices = $container->findTaggedServiceIds('sylius.metadata_renderer');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addRenderer', [new Reference($id)]);
        }
    }
}
