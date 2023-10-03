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

namespace Sylius\Bundle\PayumBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PayumBundle\Attribute\AsGatewayConfigurationType;
use Sylius\Bundle\PayumBundle\DependencyInjection\SyliusPayumExtension;
use Sylius\Bundle\PayumBundle\Tests\Stub\GatewayConfigurationTypeStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusPayumExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_gateway_configuration_type_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.gateway_configuration_type_with_attribute',
            (new Definition())
                ->setClass(GatewayConfigurationTypeStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.gateway_configuration_type_with_attribute',
            AsGatewayConfigurationType::SERVICE_TAG,
            ['type' => 'test', 'label' => 'Test', 'priority' => 15],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusPayumExtension()];
    }
}
