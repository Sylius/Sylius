<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeRequestResolverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.context.channel.request_based.resolver')) {
            return;
        }

        $requestResolverDefinition = $container->findDefinition('sylius.context.channel.request_based.resolver');

        $taggedServices = $container->findTaggedServiceIds('sylius.context.channel.request_based.resolver');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $arguments = [new Reference($id)];

                if (isset($attributes['priority'])) {
                    $arguments[] = $attributes['priority'];
                }

                $requestResolverDefinition->addMethodCall('addResolver', $arguments);
            }
        }
    }
}
