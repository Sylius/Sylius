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
use Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Bundle\Action\SwaggerUiAction;
use Sylius\Bundle\ApiBundle\DependencyInjection\SyliusApiExtension;

final class SyliusApiExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_loads_swagger_integration_if_it_is_turned_on(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->setParameter('api_platform.enable_swagger_ui', true);
        $this->load();

        $this->assertContainerBuilderHasService('api_platform.swagger.action.ui', SwaggerUiAction::class);
    }

    /** @test */
    public function it_does_not_load_swagger_integration_if_it_is_turned_off(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->setParameter('api_platform.enable_swagger_ui', false);
        $this->load();

        $this->assertContainerBuilderNotHasService('api_platform.swagger.action.ui');
    }

    /** @test */
    public function it_does_not_load_swagger_integration_if_it_does_not_exists(): void
    {
        $this->container->setParameter('kernel.bundles_metadata', ['SyliusApiBundle' => ['path' => __DIR__ . '../..']]);

        $this->load();

        $this->assertContainerBuilderNotHasService('api_platform.swagger.action.ui');
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

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusApiExtension(),
        ];
    }
}
