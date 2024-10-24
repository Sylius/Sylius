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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterTaxCalculationStrategiesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterTaxCalculationStrategiesPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_registers_strategies_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry.tax_calculation_strategy', new Definition());
        $this->setDefinition(
            'str',
            (new Definition())
                ->addTag('sylius.taxation.calculation_strategy', ['type' => 'str1', 'label' => 'Str 1'])
                ->addTag('sylius.taxation.calculation_strategy', ['type' => 'str2', 'label' => 'Str 2', 'priority' => 5]),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.tax_calculation_strategy',
            'register',
            [new Reference('str'), 0],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.tax_calculation_strategy',
            'register',
            [new Reference('str'), 5],
        );
    }

    /** @test */
    public function it_creates_parameter_which_maps_strategies(): void
    {
        $this->setDefinition('sylius.registry.tax_calculation_strategy', new Definition());
        $this->setDefinition(
            'str',
            (new Definition())
                ->addTag('sylius.taxation.calculation_strategy', ['type' => 'str1', 'label' => 'Str 1'])
                ->addTag('sylius.taxation.calculation_strategy', ['type' => 'str2', 'label' => 'Str 2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.tax_calculation_strategies',
            ['str1' => 'Str 1', 'str2' => 'Str 2'],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterTaxCalculationStrategiesPass());
    }
}
