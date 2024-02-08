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

namespace Sylius\Bundle\PayumBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\RegisterGatewayConfigTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterGatewayConfigTypePassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_registers_payment_gateways_configs_by_their_priority_in_the_registry(): void
    {
        $this->setDefinition('sylius.form_registry.payum_gateway_config', new Definition());
        $this->setDefinition(
            'custom_low_priority_gateway',
            (new Definition('LowGatewayClass'))->addTag(
                'sylius.gateway_configuration_type',
                ['type' => 'low_gateway', 'label' => 'Low Gateway', 'priority' => 10],
            ),
        );
        $this->setDefinition(
            'custom_high_priority_gateway',
            (new Definition('HighGatewayClass'))->addTag(
                'sylius.gateway_configuration_type',
                ['type' => 'high_gateway', 'label' => 'High Gateway', 'priority' => 1000],
            ),
        );
        $this->setDefinition(
            'custom_medium_priority_gateway',
            (new Definition('MediumGatewayClass'))->addTag(
                'sylius.gateway_configuration_type',
                ['type' => 'medium_gateway', 'label' => 'Medium Gateway', 'priority' => 300],
            ),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.payum_gateway_config',
            'add',
            ['gateway_config', 'low_gateway', 'LowGatewayClass'],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.payum_gateway_config',
            'add',
            ['gateway_config', 'high_gateway', 'HighGatewayClass'],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.payum_gateway_config',
            'add',
            ['gateway_config', 'medium_gateway', 'MediumGatewayClass'],
        );

        $this->assertSame(
            [
                'high_gateway' => 'High Gateway',
                'medium_gateway' => 'Medium Gateway',
                'low_gateway' => 'Low Gateway',
                'offline' => 'sylius.payum_gateway_factory.offline',
            ],
            $this->container->getParameter('sylius.gateway_factories'),
        );
    }

    /** @test */
    public function it_registers_payment_gateways_configs_with_default_priorities_in_the_registry(): void
    {
        $this->setDefinition('sylius.form_registry.payum_gateway_config', new Definition());
        $this->setDefinition(
            'custom_low_priority_gateway',
            (new Definition('LowGatewayClass'))->addTag(
                'sylius.gateway_configuration_type',
                ['type' => 'low_gateway', 'label' => 'Low Gateway', 'priority' => 10],
            ),
        );
        $this->setDefinition(
            'custom_regular_priority_gateway',
            (new Definition('RegularGatewayClass'))->addTag(
                'sylius.gateway_configuration_type',
                ['type' => 'regular_gateway', 'label' => 'Regular Gateway'],
            ),
        );
        $this->setDefinition(
            'custom_medium_priority_gateway',
            (new Definition('MediumGatewayClass'))->addTag(
                'sylius.gateway_configuration_type',
                ['type' => 'medium_gateway', 'label' => 'Medium Gateway', 'priority' => 300],
            ),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.payum_gateway_config',
            'add',
            ['gateway_config', 'low_gateway', 'LowGatewayClass'],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.payum_gateway_config',
            'add',
            ['gateway_config', 'regular_gateway', 'RegularGatewayClass'],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.payum_gateway_config',
            'add',
            ['gateway_config', 'medium_gateway', 'MediumGatewayClass'],
        );

        $this->assertSame(
            [
                'medium_gateway' => 'Medium Gateway',
                'low_gateway' => 'Low Gateway',
                'offline' => 'sylius.payum_gateway_factory.offline',
                'regular_gateway' => 'Regular Gateway',
            ],
            $this->container->getParameter('sylius.gateway_factories'),
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterGatewayConfigTypePass());
    }
}
