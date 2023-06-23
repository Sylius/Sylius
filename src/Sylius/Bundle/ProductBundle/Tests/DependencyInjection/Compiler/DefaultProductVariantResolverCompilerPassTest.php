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

namespace Sylius\Bundle\ProductBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ProductBundle\DependencyInjection\Compiler\DefaultProductVariantResolverCompilerPass;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class DefaultProductVariantResolverCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_nothing_when_default_resolver_is_not_present(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.product_variant_resolver.default');
    }

    /** @test */
    public function it_does_nothing_when_default_resolver_already_has_the_tag(): void
    {
        $defaultResolver = $this->registerService(
            'sylius.product_variant_resolver.default',
            ProductVariantResolverInterface::class,
        );
        $defaultResolver->addTag('sylius.product_variant_resolver');

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.product_variant_resolver.default',
            'sylius.product_variant_resolver',
        );
    }

    /** @test */
    public function it_adds_variant_resolver_tag_with_very_low_priority_to_the_default_resolver(): void
    {
        $this->setDefinition('sylius.product_variant_resolver.default', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.product_variant_resolver.default',
            'sylius.product_variant_resolver',
            ['priority' => -999],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DefaultProductVariantResolverCompilerPass());
    }
}
