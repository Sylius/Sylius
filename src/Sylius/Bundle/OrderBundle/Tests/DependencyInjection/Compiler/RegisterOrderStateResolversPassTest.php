<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterOrderStateResolversPass;
use Sylius\Component\Core\OrderProcessing\StateResolver\OrderStateResolver;
use Sylius\Component\Order\StateResolver\CompositeOrderStateResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class RegisterOrderStateResolversPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_composite_order_state_resolver()
    {
        $compositeOrderStateResolverDefinition = new Definition(CompositeOrderStateResolver::class);
        $this->setDefinition('sylius.order_processing.state_resolver', $compositeOrderStateResolverDefinition);

        $orderStateResolver = new Definition(OrderStateResolver::class);
        $orderStateResolver->addTag('sylius.order.state_resolver');

        $this->setDefinition('sylius.order_processing.state_resolver.order', $orderStateResolver);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.order_processing.state_resolver',
            'addResolver', [
                new Reference('sylius.order_processing.state_resolver.order'),
                0
            ]
        );
    }

    /**
     * @test
     */
    public function it_adds_method_call_to_composite_order_state_resolver_with_custom_priority()
    {
        $compositeOrderStateResolverDefinition = new Definition(CompositeOrderStateResolver::class);
        $this->setDefinition('sylius.order_processing.state_resolver', $compositeOrderStateResolverDefinition);

        $orderStateResolver = new Definition(OrderStateResolver::class);
        $orderStateResolver->addTag('sylius.order.state_resolver', ['priority' => 10]);

        $this->setDefinition('sylius.order_processing.state_resolver.order', $orderStateResolver);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.order_processing.state_resolver',
            'addResolver', [
                new Reference('sylius.order_processing.state_resolver.order'),
                10
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_call_if_there_are_no_tagged_resolvers()
    {
        $compositeOrderStateResolverDefinition = new Definition(CompositeOrderStateResolver::class);
        $this->setDefinition('sylius.order_processing.state_resolver', $compositeOrderStateResolverDefinition);

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.order_processing.state_resolver',
            'addResolver'
        );
    }

    /**
     * @param string $serviceId
     * @param string $method
     */
    private function assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall($serviceId, $method)
    {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new \PHPUnit_Framework_Constraint_Not(new DefinitionHasMethodCallConstraint($method))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterOrderStateResolversPass());
    }
}
