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
use Sylius\Bundle\OrderBundle\Tests\Stub\CartContextWithAttributeStub;
use Sylius\Bundle\OrderBundle\Tests\Stub\OrderProcessorWithAttributeStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusOrderExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_cart_context_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.cart_context_autoconfigured',
            (new Definition())
                ->setClass(CartContextWithAttributeStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.cart_context_autoconfigured',
            RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG,
            ['priority' => 20],
        );
    }

    /** @test */
    public function it_autoconfigures_order_processors_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.processor_autoconfigured',
            (new Definition())
                ->setClass(OrderProcessorWithAttributeStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.processor_autoconfigured',
            RegisterProcessorsPass::PROCESSOR_SERVICE_TAG,
            ['priority' => 10],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusOrderExtension()];
    }
}
