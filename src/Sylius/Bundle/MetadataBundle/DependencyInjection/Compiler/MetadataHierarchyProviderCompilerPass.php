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
class MetadataHierarchyProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.metadata.hierarchy_provider')) {
            return;
        }

        $definition = $container->getDefinition('sylius.metadata.hierarchy_provider');

        $taggedServices = $container->findTaggedServiceIds('sylius.metadata_hierarchy_provider');

        $hierarchyProviders = $definition->getArgument(0) ?: [];
        foreach ($taggedServices as $id => $tags) {
            $hierarchyProviders[] = new Reference($id);
        }

        $definition->replaceArgument(0, $hierarchyProviders);
    }
}
