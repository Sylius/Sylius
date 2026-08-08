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

namespace Sylius\Bundle\ApiBundle\DependencyInjection;

use Sylius\Bundle\ApiBundle\Attribute\AsDocumentationModifier;
use Sylius\Bundle\ApiBundle\Attribute\AsPaymentConfigurationProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusApiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('sylius_api.enabled', $config['enabled']);
        $container->setParameter('sylius_api.endpoints.admin_enabled', $config['endpoints']['admin_enabled']);
        $container->setParameter('sylius_api.endpoints.shop_enabled', $config['endpoints']['shop_enabled']);
        $container->setParameter('sylius_api.default_image_filter', $config['default_image_filter']);
        $container->setParameter(
            'sylius_api.filter_eager_loading_extension.restricted_resources',
            $config['filter_eager_loading_extension']['restricted_resources'],
        );
        $container->setParameter('sylius_api.order_states_to_filter_out', $config['order_states_to_filter_out']);
        $container->setParameter('sylius_api.operations_to_remove', $config['operations_to_remove']);

        $loader->load('services.xml');

        // If parameter is not set, it means that Swagger is not enabled (api_platform.enable_swagger set to false)
        $swaggerEnabled = $container->hasParameter('api_platform.swagger.api_keys');

        if ($swaggerEnabled) {
            $loader->load('integrations/swagger.xml');
        }

        $this->registerAutoconfiguration($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependApiPlatformMapping($container);
    }

    private function prependApiPlatformMapping(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        /** @var array<string, array<string, string>> $metadata */
        $metadata = $container->getParameter('kernel.bundles_metadata');

        $apiPlatformPath = $metadata['SyliusApiBundle']['path'] . '/Resources/config/api_platform';

        $paths = [];

        if ($config['endpoints']['admin_enabled']) {
            $paths[] = $apiPlatformPath . '/resources/admin';
        }

        if ($config['endpoints']['shop_enabled']) {
            $paths[] = $apiPlatformPath . '/resources/shop';
        }

        if (empty($paths)) {
            return;
        }

        array_unshift($paths, $apiPlatformPath . '/properties');

        $container->prependExtensionConfig('api_platform', ['mapping' => ['paths' => $paths]]);
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsDocumentationModifier::class,
            static function (ChildDefinition $definition, AsDocumentationModifier $attribute): void {
                $definition->addTag(AsDocumentationModifier::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsPaymentConfigurationProvider::class,
            static function (ChildDefinition $definition, AsPaymentConfigurationProvider $attribute): void {
                $definition->addTag(AsPaymentConfigurationProvider::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );
    }
}
