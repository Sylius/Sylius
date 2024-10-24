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

namespace Sylius\Bundle\PaymentBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\PaymentBundle\DependencyInjection\Compiler\RegisterPaymentMethodsResolversPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPaymentMethodsResolversPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_registers_resolvers_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry.payment_methods_resolver', new Definition());
        $this->setDefinition(
            'res',
            (new Definition())
                ->addTag('sylius.payment_method_resolver', ['type' => 'res1', 'label' => 'Res 1'])
                ->addTag('sylius.payment_method_resolver', ['type' => 'res2', 'label' => 'Res 2', 'priority' => 5]),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.payment_methods_resolver',
            'register',
            [new Reference('res'), 0],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry.payment_methods_resolver',
            'register',
            [new Reference('res'), 5],
        );
    }

    /** @test */
    public function it_creates_parameter_which_maps_resolvers(): void
    {
        $this->setDefinition('sylius.registry.payment_methods_resolver', new Definition());
        $this->setDefinition(
            'res',
            (new Definition())
                ->addTag('sylius.payment_method_resolver', ['type' => 'res1', 'label' => 'Res 1'])
                ->addTag('sylius.payment_method_resolver', ['type' => 'res2', 'label' => 'Res 2', 'priority' => 5]),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.payment_method_resolvers',
            ['res1' => 'Res 1', 'res2' => 'Res 2'],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterPaymentMethodsResolversPass());
    }
}
