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
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterProcessorsPass;
use Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer;
use Sylius\Component\Order\Processor\CompositeOrderProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class RegisterProcessorPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_composite_order_processor_if_exist()
    {
        $compositeOrderProcessorDefinition = new Definition(CompositeOrderProcessor::class);
        $this->setDefinition('sylius.order_processing.order_processor', $compositeOrderProcessorDefinition);

        $orderAdjustmentClearerDefinition = new Definition(OrderAdjustmentsClearer::class);
        $orderAdjustmentClearerDefinition->addTag('sylius.order_processor');

        $this->setDefinition('sylius.order_processing.order_adjustments_clearer', $orderAdjustmentClearerDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.order_processing.order_processor',
            'addProcessor', [
                new Reference('sylius.order_processing.order_adjustments_clearer'),
                0
            ]
        );
    }

    /**
     * @test
     */
    public function it_adds_method_call_to_composite_order_processor_with_custom_priority()
    {
        $compositeOrderProcessorDefinition = new Definition(CompositeOrderProcessor::class);
        $this->setDefinition('sylius.order_processing.order_processor', $compositeOrderProcessorDefinition);

        $orderAdjustmentClearerDefinition = new Definition(OrderAdjustmentsClearer::class);
        $orderAdjustmentClearerDefinition->addTag('sylius.order_processor', ['priority' => 10]);

        $this->setDefinition('sylius.order_processing.order_adjustments_clearer', $orderAdjustmentClearerDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.order_processing.order_processor',
            'addProcessor', [
                new Reference('sylius.order_processing.order_adjustments_clearer'),
                10
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_call_if_there_are_no_tagged_processors()
    {
        $compositeOrderProcessorDefinition = new Definition(CompositeOrderProcessor::class);
        $this->setDefinition('sylius.order_processing.order_processor', $compositeOrderProcessorDefinition);

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.order_processing.order_processor',
            'addProcessor'
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
        $container->addCompilerPass(new RegisterProcessorsPass());
    }
}
