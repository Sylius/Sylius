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

namespace Sylius\Bundle\PromotionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\SetCatalogPromotionScopeTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class SetCatalogPromotionScopeTypesPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_sets_scope_types_parameter(): void
    {
        $this->setDefinition(
            'variant_checker',
            (new Definition())
                ->addTag('sylius.catalog_promotion.variant_checker', ['type' => 'custom'])
                ->addTag('sylius.catalog_promotion.variant_checker', ['type' => 'another_custom']),
        );
        $this->setDefinition(
            'second_variant_checker',
            (new Definition())
                ->addTag('sylius.catalog_promotion.variant_checker', ['type' => 'second_custom']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.catalog_promotion.scopes_types',
            ['custom', 'another_custom', 'second_custom'],
        );
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_type_attribute_defined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tagged catalog promotion variant checker `variant_checker` needs to have `type` attribute.');

        $this->setDefinition(
            'variant_checker',
            (new Definition())
                ->addTag('sylius.catalog_promotion.variant_checker', []),
        );

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SetCatalogPromotionScopeTypesPass());
    }
}
