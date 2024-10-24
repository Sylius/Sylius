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
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\SetCatalogPromotionActionTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class SetCatalogPromotionActionTypesPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_sets_action_types_parameter(): void
    {
        $this->setDefinition(
            'price_calculator',
            (new Definition())
                ->addTag('sylius.catalog_promotion.price_calculator', ['type' => 'custom'])
                ->addTag('sylius.catalog_promotion.price_calculator', ['type' => 'another_custom']),
        );
        $this->setDefinition(
            'second_price_calculator',
            (new Definition())
                ->addTag('sylius.catalog_promotion.price_calculator', ['type' => 'second_custom']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.catalog_promotion.actions_types',
            ['custom', 'another_custom', 'second_custom'],
        );
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_type_attribute_defined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tagged catalog promotion price calculator `price_calculator` needs to have `type` attribute.');

        $this->setDefinition(
            'price_calculator',
            (new Definition())
                ->addTag('sylius.catalog_promotion.price_calculator', []),
        );

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SetCatalogPromotionActionTypesPass());
    }
}
