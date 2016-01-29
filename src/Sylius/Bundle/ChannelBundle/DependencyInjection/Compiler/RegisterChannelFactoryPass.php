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

use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RegisterChannelFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.factory.channel')) {
            return;
        }

        $baseChannelFactoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('sylius.model.channel.class'),
            ]
        );

        $channelFactoryDefinition = new Definition(
            $container->getParameter('sylius.factory.channel.class'),
            [
                $baseChannelFactoryDefinition,
            ]
        );

        $container->setDefinition('sylius.factory.channel', $channelFactoryDefinition);
    }
}
