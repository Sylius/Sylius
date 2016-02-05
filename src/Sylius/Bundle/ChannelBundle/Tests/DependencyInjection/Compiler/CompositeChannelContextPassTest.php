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
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
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
        $this->setDefinition('sylius.context.channel.composite', new Definition());
        $this->setDefinition(
            'sylius.context.channel.tagged_one',
            (new Definition())->addTag('sylius.context.channel')
        );

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.context.channel', 'sylius.context.channel.composite');
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
        $this->setDefinition('sylius.context.channel.composite', new Definition());
        $this->setDefinition(
            'sylius.context.channel.tagged_one',
            (new Definition())->addTag('sylius.context.channel', ['priority' => 42])
        );

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.context.channel', 'sylius.context.channel.composite');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.context.channel',
            'addContext',
            [new Reference('sylius.context.channel.tagged_one'), 42]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_calls_to_the_overriding_service_if_the_composite_service_is_overridden()
    {
        $this->setDefinition('sylius.context.channel', new Definition());
        $this->setDefinition('sylius.context.channel.composite', new Definition());
        $this->setDefinition(
            'sylius.context.channel.tagged_one',
            (new Definition())->addTag('sylius.context.channel')
        );

        $this->compile();

        $this->assertContainerBuilderNotHasServiceDefinitionWithMethodCall(
            'sylius.context.channel',
            'addContext',
            [new Reference('sylius.context.channel.tagged_one')]
        );
    }

    /**
     * @test
     */
    public function it_still_adds_method_calls_to_composite_context_even_if_it_was_overridden()
    {
        $this->setDefinition('sylius.context.channel', new Definition());
        $this->setDefinition('sylius.context.channel.composite', new Definition());
        $this->setDefinition(
            'sylius.context.channel.tagged_one',
            (new Definition())->addTag('sylius.context.channel')
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.context.channel.composite',
            'addContext',
            [new Reference('sylius.context.channel.tagged_one')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompositeChannelContextPass());
    }

    /**
     * @param string $serviceId
     * @param string $method
     * @param array $arguments
     */
    private function assertContainerBuilderNotHasServiceDefinitionWithMethodCall($serviceId, $method, $arguments)
    {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new \PHPUnit_Framework_Constraint_Not(new DefinitionHasMethodCallConstraint($method, $arguments))
        );
    }
}
