<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler\CompositeChannelContextPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CompositeChannelContextPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_collects_tagged_channel_contexts()
    {
        $this->setDefinition('sylius.context.channel', new Definition());
        $this->setDefinition(
            'sylius.context.channel.tagged_one',
            (new Definition())->addTag('sylius.context.channel')
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.context.channel',
            'addContext',
            [new Reference('sylius.context.channel.tagged_one')]
        );
    }

    /**
     * @test
     */
    public function it_collects_tagged_channel_contexts_with_priority()
    {
        $this->setDefinition('sylius.context.channel', new Definition());
        $this->setDefinition(
            'sylius.context.channel.tagged_one',
            (new Definition())->addTag('sylius.context.channel', ['priority' => 42])
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.context.channel',
            'addContext',
            [new Reference('sylius.context.channel.tagged_one'), 42]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompositeChannelContextPass());
    }
}
