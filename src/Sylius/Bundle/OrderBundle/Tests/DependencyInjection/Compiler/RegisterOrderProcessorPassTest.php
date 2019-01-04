<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\OrderBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use PHPUnit\Framework\Constraint\LogicalNot;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterProcessorsPass;
use Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer;
use Sylius\Component\Order\Processor\CompositeOrderProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterOrderProcessorPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_composite_order_processor_if_exist(): void
    {
        $compositeOrderProcessorDefinition = new Definition(CompositeOrderProcessor::class);
        $this->setDefinition('sylius.order_processing.order_processor.composite', $compositeOrderProcessorDefinition);

        $orderAdjustmentClearerDefinition = new Definition(OrderAdjustmentsClearer::class);
        $orderAdjustmentClearerDefinition->addTag('sylius.order_processor');

        $this->setDefinition('sylius.order_processing.order_adjustments_clearer', $orderAdjustmentClearerDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.order_processing.order_processor',
            'addProcessor', [
                new Reference('sylius.order_processing.order_adjustments_clearer'),
                0,
            ]
        );
    }

    /**
     * @test
     */
    public function it_adds_method_call_to_composite_order_processor_with_custom_priority(): void
    {
        $compositeOrderProcessorDefinition = new Definition(CompositeOrderProcessor::class);
        $this->setDefinition('sylius.order_processing.order_processor.composite', $compositeOrderProcessorDefinition);

        $orderAdjustmentClearerDefinition = new Definition(OrderAdjustmentsClearer::class);
        $orderAdjustmentClearerDefinition->addTag('sylius.order_processor', ['priority' => 10]);

        $this->setDefinition('sylius.order_processing.order_adjustments_clearer', $orderAdjustmentClearerDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.order_processing.order_processor',
            'addProcessor', [
                new Reference('sylius.order_processing.order_adjustments_clearer'),
                10,
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_call_if_there_are_no_tagged_processors(): void
    {
        $compositeOrderProcessorDefinition = new Definition(CompositeOrderProcessor::class);
        $this->setDefinition('sylius.order_processing.order_processor', $compositeOrderProcessorDefinition);

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.order_processing.order_processor',
            'addProcessor'
        );
    }

    private function assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(string $serviceId, string $method): void
    {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new LogicalNot(new DefinitionHasMethodCallConstraint($method))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterProcessorsPass());
    }
}
