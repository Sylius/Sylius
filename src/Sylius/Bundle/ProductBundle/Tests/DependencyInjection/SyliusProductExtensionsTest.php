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

namespace Sylius\Bundle\ProductBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ProductBundle\DependencyInjection\SyliusProductExtension;
use Sylius\Bundle\ProductBundle\Tests\Stub\ProductVariantResolverStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusProductExtensionsTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_product_variant_resolver_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.product_variant_resolver_autoconfigured',
            (new Definition())
                ->setClass(ProductVariantResolverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.product_variant_resolver_autoconfigured',
            'sylius.product_variant_resolver',
            ['priority' => 50],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusProductExtension()];
    }
}
