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

namespace Tests\Sylius\Bundle\ProductBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\ProductBundle\DependencyInjection\SyliusProductExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\ProductBundle\Stub\ProductVariantResolverStub;

final class SyliusProductExtensionsTest extends AbstractExtensionTestCase
{
    #[Test]
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
