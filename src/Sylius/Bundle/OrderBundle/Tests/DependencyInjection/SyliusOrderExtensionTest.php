<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use Sylius\Component\Order\Context\CartContextInterface;
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

    protected function getContainerExtensions(): array
    {
        return [new SyliusOrderExtension()];
    }
}
