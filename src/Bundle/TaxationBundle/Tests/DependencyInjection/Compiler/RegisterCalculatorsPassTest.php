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

namespace Sylius\Bundle\TaxationBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterCalculatorsPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_registers_calculators_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry.tax_calculator', new Definition());
        $this->setDefinition(
            'custom_calc',
            (new Definition())
                ->addTag('sylius.tax_calculator', ['calculator' => 'calc1'])
                ->addTag('sylius.tax_calculator', ['calculator' => 'calc2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.tax_calculator',
            'register',
            ['calc1', new Reference('custom_calc')],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.tax_calculator',
            'register',
            ['calc2', new Reference('custom_calc')],
        );
    }

    /** @test */
    public function it_creates_parameter_which_maps_calculators(): void
    {
        $this->setDefinition('sylius.registry.tax_calculator', new Definition());
        $this->setDefinition(
            'custom_calc',
            (new Definition())
                ->addTag('sylius.tax_calculator', ['calculator' => 'calc1'])
                ->addTag('sylius.tax_calculator', ['calculator' => 'calc2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.tax_calculators',
            ['calc1' => 'calc1', 'calc2' => 'calc2'],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterCalculatorsPass());
    }
}
