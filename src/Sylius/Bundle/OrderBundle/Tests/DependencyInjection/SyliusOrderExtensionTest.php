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
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusOrderExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_cart_contexts_and_order_processors(): void
    {
        $this->markTestSkipped('Symfony 4.4 makes it impossible to test autoconfigured services this way.');

        $this->container->setDefinition(
            'acme.cart_context_autoconfigured',
            (new Definition())
                ->setClass($this->getMockClass(CartContextInterface::class))
                ->setAutoconfigured(true)
        );

        $this->container->setDefinition(
            'acme.processor_autoconfigured',
            (new Definition())
                ->setClass($this->getMockClass(OrderProcessorInterface::class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.cart_context_autoconfigured',
            RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.processor_autoconfigured',
            RegisterProcessorsPass::PROCESSOR_SERVICE_TAG
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusOrderExtension()];
    }
}
