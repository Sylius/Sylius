<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Factory\ChannelFactory;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RegisterChannelFactoryPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler\RegisterChannelFactoryPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_creates_default_definition_of_channel_factory(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('sylius.factory.channel')->willReturn(true);

        $baseChannelFactoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('sylius.model.channel.class'),
            ]
        );

        $container->getParameter('sylius.factory.channel.class')->willReturn(ChannelFactory::class);

        $channelFactoryDefinition = new Definition(
            ChannelFactory::class,
            [
                $baseChannelFactoryDefinition,
            ]
        );

        $container->setDefinition('sylius.factory.channel', $channelFactoryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_create_default_definition_of_channel_factory_if_channel_factory_is_not_set(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('sylius.factory.channel')->willReturn(false);

        $baseChannelFactoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('sylius.model.channel.class'),
            ]
        );

        $container->getParameter('sylius.factory.channel.class')->shouldNotBeCalled();

        $channelFactoryDefinition = new Definition(
            ChannelFactory::class,
            [
                $baseChannelFactoryDefinition,
            ]
        );

        $container->setDefinition('sylius.factory.channel', $channelFactoryDefinition)->shouldNotBeCalled();

        $this->process($container);
    }
}
