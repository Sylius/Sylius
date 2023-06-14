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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/** @experimental */
final class SyliusApiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('sylius_api.enabled', $config['enabled']);
        $container->setParameter('sylius_api.legacy_error_handling', $config['legacy_error_handling']);
        $container->setParameter('sylius_api.product_image_prefix', $config['product_image_prefix']);
        $container->setParameter(
            'sylius_api.filter_eager_loading_extension.restricted_resources',
            $config['filter_eager_loading_extension']['restricted_resources'],
        );

        $loader->load('services.xml');

        if ($container->hasParameter('api_platform.enable_swagger_ui') && $container->getParameter('api_platform.enable_swagger_ui')) {
            $loader->load('integrations/swagger.xml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependApiPlatformMapping($container);
    }

    private function prependApiPlatformMapping(ContainerBuilder $container): void
    {
        /** @var array<string, array<string, string>> $metadata */
        $metadata = $container->getParameter('kernel.bundles_metadata');

        $path = $metadata['SyliusApiBundle']['path'] . '/Resources/config/api_resources';

        $container->prependExtensionConfig('api_platform', ['mapping' => ['paths' => [$path]]]);
    }
}
