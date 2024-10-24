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

namespace Sylius\Bundle\ShippingBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterCalculatorsPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_registers_calculators_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry.shipping_calculator', new Definition());
        $this->setDefinition('sylius.form_registry.shipping_calculator', new Definition());
        $this->setDefinition(
            'custom_calc',
            (new Definition())
                ->addTag('sylius.shipping_calculator', ['calculator' => 'calc1', 'label' => 'Calc 1'])
                ->addTag('sylius.shipping_calculator', ['calculator' => 'calc2', 'label' => 'Calc 2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.shipping_calculator',
            'register',
            ['calc1', new Reference('custom_calc')],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.shipping_calculator',
            'register',
            ['calc2', new Reference('custom_calc')],
        );
    }

    /** @test */
    public function it_registers_calculators_in_the_form_type_registry(): void
    {
        $this->setDefinition('sylius.registry.shipping_calculator', new Definition());
        $this->setDefinition('sylius.form_registry.shipping_calculator', new Definition());
        $this->setDefinition(
            'custom_calc',
            (new Definition())
                ->addTag('sylius.shipping_calculator', ['calculator' => 'calc1', 'label' => 'Calc 1'])
                ->addTag('sylius.shipping_calculator', ['calculator' => 'calc2', 'label' => 'Calc 2', 'form_type' => 'FQCN']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.shipping_calculator',
            'add',
            ['calc2', 'default', 'FQCN'],
        );
    }

    /** @test */
    public function it_creates_parameter_which_maps_calculators(): void
    {
        $this->setDefinition('sylius.registry.shipping_calculator', new Definition());
        $this->setDefinition('sylius.form_registry.shipping_calculator', new Definition());
        $this->setDefinition(
            'custom_calc',
            (new Definition())
                ->addTag('sylius.shipping_calculator', ['calculator' => 'calc1', 'label' => 'Calc 1'])
                ->addTag('sylius.shipping_calculator', ['calculator' => 'calc2', 'label' => 'Calc 2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.shipping_calculators',
            ['calc1' => 'Calc 1', 'calc2' => 'Calc 2'],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterCalculatorsPass());
    }
}
