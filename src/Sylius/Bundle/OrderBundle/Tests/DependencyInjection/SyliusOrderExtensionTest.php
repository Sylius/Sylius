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

namespace Sylius\Bundle\OrderBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterProcessorsPass;
use Sylius\Bundle\OrderBundle\DependencyInjection\SyliusOrderExtension;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\Attribute\AsCartContext;
use Sylius\Component\Order\Attribute\AsOrderProcessor;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusOrderExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_cart_contexts(): void
    {
        $this->container->setDefinition(
            'acme.cart_context_autoconfigured',
            (new Definition())
                ->setClass($this->getMockClass(CartContextInterface::class))
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.cart_context_autoconfigured',
            RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG,
        );
    }

    /**
     * @test
     */
    public function it_does_not_autoconfigure_order_processors(): void
    {
        $this->container->setDefinition(
            'acme.processor_autoconfigured',
            (new Definition())
                ->setClass($this->getMockClass(OrderProcessorInterface::class))
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.processor_autoconfigured',
            RegisterProcessorsPass::PROCESSOR_SERVICE_TAG,
        );
    }

    /** @test */
    public function it_autoconfigures_cart_contexts_with_attribute(): void
    {
        $this->container->register(
            'acme.cart_context_autoconfigured',
            DummyCartContext::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_cart_context_autoconfigured',
            PrioritizedDummyCartContext::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.cart_context_autoconfigured',
            RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_cart_context_autoconfigured',
            RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG,
            ['priority' => 256]
        );
    }

    /** @test */
    public function it_autoconfigures_order_processors_with_attribute(): void
    {
        $this->container->register(
            'acme.order_processor_autoconfigured',
            DummyOrderProcessor::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_order_processor_autoconfigured',
            PrioritizedDummyOrderProcessor::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.order_processor_autoconfigured',
            RegisterProcessorsPass::PROCESSOR_SERVICE_TAG
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_order_processor_autoconfigured',
            RegisterProcessorsPass::PROCESSOR_SERVICE_TAG,
            ['priority' => 256]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusOrderExtension()];
    }
}

#[AsCartContext]
class DummyCartContext implements CartContextInterface
{
    public function getCart(): OrderInterface
    {
        return new Order();
    }
}

#[AsCartContext(priority: 256)]
class PrioritizedDummyCartContext implements CartContextInterface
{
    public function getCart(): OrderInterface
    {
        return new Order();
    }
}

#[AsOrderProcessor]
class DummyOrderProcessor implements OrderProcessorInterface
{
    public function process(OrderInterface $order): void
    {
    }
}

#[AsOrderProcessor(priority: 256)]
class PrioritizedDummyOrderProcessor implements OrderProcessorInterface
{
    public function process(OrderInterface $order): void
    {
    }
}
