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
final class CompositeChannelContextPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.context.channel')) {
            return;
        }

        $channelContextDefinition = $container->findDefinition('sylius.context.channel');

        $taggedServices = $container->findTaggedServiceIds('sylius.context.channel');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $arguments = [new Reference($id)];

                if (isset($attributes['priority'])) {
                    $arguments[] = $attributes['priority'];
                }

                $channelContextDefinition->addMethodCall('addContext', $arguments);
            }
        }
    }
}
