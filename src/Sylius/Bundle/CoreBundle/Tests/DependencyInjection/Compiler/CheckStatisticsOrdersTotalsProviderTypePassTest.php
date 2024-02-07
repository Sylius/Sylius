<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\CheckStatisticsOrdersTotalsProviderTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CheckStatisticsOrdersTotalsProviderTypePassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_passes_when_all_providers_are_present(): void
    {
        $this->setParameter('sylius_core.orders_statistics.intervals_map', ['daily' => 'Daily', 'monthly' => 'Monthly']);
        $this->setDefinition(
            'sylius.registry.statistics_orders_totals_provider',
            (new Definition())
                ->addTag('sylius.statistics.orders_totals_provider', ['type' => 'daily'])
                ->addTag('sylius.statistics.orders_totals_provider', ['type' => 'monthly']),
        );

        $this->compile();
        $this->assertTrue(true, 'Compiler pass completed without throwing an exception.');
    }

    /** @test */
    public function it_throws_exception_if_statistics_orders_totals_provider_type_is_not_defined(): void
    {
        $this->setParameter('sylius_core.orders_statistics.intervals_map', ['daily' => 'Daily', 'monthly' => 'Monthly']);
        $this->setDefinition(
            'sylius.registry.statistics_orders_totals_provider',
            (new Definition())
                ->addTag('sylius.statistics.orders_totals_provider', ['type' => 'daily'])
                ->addTag('sylius.statistics.orders_totals_provider', []),
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tagged orders totals providers need to have `type` attribute.');

        $this->compile();
    }


    /** @test */
    public function it_throws_exception_if_statistics_orders_totals_provider_type_is_incorrect(): void
    {
        $this->setParameter('sylius_core.orders_statistics.intervals_map', ['daily' => 'Daily', 'monthly' => 'Monthly']);
        $this->setDefinition(
            'sylius.registry.statistics_orders_totals_provider',
            (new Definition())
                ->addTag('sylius.statistics.orders_totals_provider', ['type' => 'daily']),
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no orders totals provider for interval type "monthly"');

        $this->compile();
    }


    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CheckStatisticsOrdersTotalsProviderTypePass());
    }
}
