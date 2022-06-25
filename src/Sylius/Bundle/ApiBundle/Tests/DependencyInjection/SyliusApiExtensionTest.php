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

namespace Sylius\Bundle\ApiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Bundle\Action\SwaggerUiAction;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Sylius\Bundle\ApiBundle\DependencyInjection\SyliusApiExtension;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_loads_swagger_integration_if_it_is_turned_on()
    {
        $this->setParameter('api_platform.enable_swagger_ui', true);
        $this->load();

        $this->assertContainerBuilderHasService('api_platform.swagger.action.ui', SwaggerUiAction::class);
    }

    /**
     * @test
     */
    public function it_does_not_load_swagger_integration_if_it_is_turned_off()
    {
        $this->setParameter('api_platform.enable_swagger_ui', false);
        $this->load();

        $this->assertContainerBuilderNotHasService('api_platform.swagger.action.ui');
    }

    /**
     * @test
     */
    public function it_does_not_load_swagger_integration_if_it_does_not_exists()
    {
        $this->load();

        $this->assertContainerBuilderNotHasService('api_platform.swagger.action.ui');
    }

    /** @test */
    public function it_loads_filter_eager_loading_extension_restricted_operations_configuration_properly(): void
    {
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
        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_api.filter_eager_loading_extension.restricted_resources', []);
    }

    /** @test */
    public function it_autoconfigures_command_api_data_transformer(): void
    {
        $this->container->setDefinition(
            'acme.api_command_data_transformer_autoconfigured',
            (new Definition())
                ->setClass(self::getMockClass(CommandDataTransformerInterface::class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.api_command_data_transformer_autoconfigured',
            'sylius.api.command_data_transformer'
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusApiExtension(),
        ];
    }
}
