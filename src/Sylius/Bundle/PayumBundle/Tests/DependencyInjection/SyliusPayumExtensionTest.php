<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PayumBundle\DependencyInjection\SyliusPayumExtension;
use Sylius\Component\Payum\Attribute\AsGatewayConfigurationType;
use Symfony\Component\Form\AbstractType;

final class SyliusPayumExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_gateway_configuration_type_with_attribute(): void
    {
        $this->container->register(
            'acme.gateway_configuration_type_autoconfigured',
            DummyGatewayConfigurationType::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_gateway_configuration_type_autoconfigured',
            PrioritizedDummyCartContext::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.gateway_configuration_type_autoconfigured',
            'sylius.gateway_configuration_type',
            ['type' => 'dummy', 'label' => 'dummy', 'priority' => 0]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_gateway_configuration_type_autoconfigured',
            'sylius.gateway_configuration_type',
            ['type' => 'dummy', 'label' => 'dummy', 'priority' => 16]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusPayumExtension()];
    }
}

#[AsGatewayConfigurationType(type: 'dummy', label: 'dummy')]
class DummyGatewayConfigurationType extends AbstractType
{
}

#[AsGatewayConfigurationType(type: 'dummy', label: 'dummy', priority: 16)]
class PrioritizedDummyCartContext extends AbstractType
{
}
