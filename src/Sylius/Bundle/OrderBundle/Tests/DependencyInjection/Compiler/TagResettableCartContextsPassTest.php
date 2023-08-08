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

namespace DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasTagConstraint;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\TagResettableCartContextsPass;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Contracts\Service\ResetInterface;

final class TagResettableCartContextsPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_tags_resetting_cart_contexts_with_kernel_reset(): void
    {
        $this->container->setDefinition(
            'acme.cart_context_resetting',
            (new Definition())
                ->setClass($this->getMockClass(ResetInterface::class))
                ->setAutoconfigured(true)
                ->addTag(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.cart_context_resetting',
            'kernel.reset',
            ['method' => 'reset'],
        );
    }

    /** @test */
    public function it_tags_only_if_service_implements_resetting_cart_context(): void
    {
        $this->container->setDefinition(
            'acme.cart_context',
            (new Definition())
                ->setClass($this->getMockClass(CartContextInterface::class))
                ->setAutoconfigured(true)
                ->addTag(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG),
        );

        $this->compile();

        $definition = $this->container->findDefinition('acme.cart_context');

        self::assertThat($definition, self::logicalNot(new DefinitionHasTagConstraint('kernel.reset', ['method' => 'reset'])));
    }

    /** @test */
    public function it_tags_only_if_service_is_tagged_as_cart_context(): void
    {
        $this->container->setDefinition(
            'acme.cart_context_resetting',
            (new Definition())
                ->setClass($this->getMockClass(ResetInterface::class))
                ->setAutoconfigured(true),
        );

        $this->compile();

        $definition = $this->container->findDefinition('acme.cart_context_resetting');

        self::assertThat($definition, self::logicalNot(new DefinitionHasTagConstraint('kernel.reset', ['method' => 'reset'])));
    }

    /** @test */
    public function it_prevents_from_tagging_cart_context_when_already_tagged_as_kernel_reset(): void
    {
        $this->container->setDefinition(
            'acme.cart_context_resetting',
            (new Definition())
                ->setClass($this->getMockClass(ResetInterface::class))
                ->setAutoconfigured(true)
                ->addTag(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG),
        );
        $this->container->registerForAutoconfiguration(ResetInterface::class)->addTag('kernel.reset', ['method' => 'reset']);

        $this->compile();

        $this->assertCount(1, $this->container->getDefinition('acme.cart_context_resetting')->getTag('kernel.reset'));
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TagResettableCartContextsPass());
    }
}
