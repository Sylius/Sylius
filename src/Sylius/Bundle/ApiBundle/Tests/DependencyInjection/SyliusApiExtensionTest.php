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

namespace Sylius\Bundle\ApiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ApiBundle\Attribute\AsCommandDataTransformer;
use Sylius\Bundle\ApiBundle\Attribute\AsDocumentationModifier;
use Sylius\Bundle\ApiBundle\Attribute\AsPaymentConfigurationProvider;
use Sylius\Bundle\ApiBundle\DependencyInjection\SyliusApiExtension;
use Sylius\Bundle\ApiBundle\Tests\Stub\CommandDataTransformerStub;
use Sylius\Bundle\ApiBundle\Tests\Stub\DocumentationModifierStub;
use Sylius\Bundle\ApiBundle\Tests\Stub\PaymentConfigurationProviderStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusApiExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_loads_swagger_integration_if_it_is_turned_on(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->setParameter('api_platform.swagger.api_keys', []);
        $this->load();

        $this->assertContainerBuilderHasService('Sylius\Bundle\ApiBundle\OpenApi\Documentation\AcceptLanguageHeaderDocumentationModifier');
    }

    /** @test */
    public function it_does_not_load_swagger_integration_if_it_is_turned_off(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->load();

        $this->assertContainerBuilderNotHasService('Sylius\Bundle\ApiBundle\OpenApi\Documentation\AcceptLanguageHeaderDocumentationModifier');
    }

    /** @test */
    public function it_loads_filter_eager_loading_extension_restricted_operations_configuration_properly(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->load([
            'filter_eager_loading_extension' => [
                'restricted_resources' => [
                    'FirstResourceClass' => [
                        'operations' => [
                            'shop_get' => [],
                            'admin_get' => null,
                        ],
                    ],
                    'SecondResourceClass' => [
                        'operations' => [
                            'shop_get' => false,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius_api.filter_eager_loading_extension.restricted_resources',
            [
                'FirstResourceClass' => [
                    'operations' => [
                        'shop_get' => ['enabled' => true],
                        'admin_get' => ['enabled' => true],
                    ],
                ],
                'SecondResourceClass' => [
                    'operations' => [
                        'shop_get' => ['enabled' => false],
                    ],
                ],
            ],
        );
    }

    /** @test */
    public function it_loads_default_filter_eager_loading_extension_restricted_operations_configuration_properly(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_api.filter_eager_loading_extension.restricted_resources', []);
    }

    /** @test */
    public function it_prepends_configuration_with_api_platform_mapping(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->load();

        $apiPlatformConfig = $this->container->getExtensionConfig('api_platform')[0];

        $this->assertSame($apiPlatformConfig['mapping']['paths'], [
            __DIR__ . '../../Resources/config/api_resources',
        ]);
    }

    /** @test */
    public function it_autoconfigures_command_data_transformer_with_attribute(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);
        $this->container->setDefinition(
            'acme.command_data_transformer_with_attribute',
            (new Definition())
                ->setClass(CommandDataTransformerStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.command_data_transformer_with_attribute',
            AsCommandDataTransformer::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    /** @test */
    public function it_autoconfigures_documentation_modifier_with_attribute(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);
        $this->container->setDefinition(
            'acme.documentation_modifier_with_attribute',
            (new Definition())
                ->setClass(DocumentationModifierStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.documentation_modifier_with_attribute',
            AsDocumentationModifier::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    /** @test */
    public function it_autoconfigures_payment_configuration_provider_with_attribute(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);
        $this->container->setDefinition(
            'acme.payment_configuration_provider_with_attribute',
            (new Definition())
                ->setClass(PaymentConfigurationProviderStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.payment_configuration_provider_with_attribute',
            AsPaymentConfigurationProvider::SERVICE_TAG,
            ['priority' => 5],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusApiExtension(),
        ];
    }
}
