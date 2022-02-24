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

namespace Sylius\Bundle\ApiBundle\DependencyInjection;

use Sylius\Bundle\ApiBundle\Cache\VarnishPurger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/** @experimental */
final class SyliusApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('sylius_api.enabled', $config['enabled']);
        $container->setParameter('sylius_api.product_image_prefix', $config['product_image_prefix']);
        $container->setParameter(
            'sylius_api.filter_eager_loading_extension.restricted_resources',
            $config['filter_eager_loading_extension']['restricted_resources'],
        );

        $loader->load('services.xml');

        if ($container->hasParameter('api_platform.enable_swagger_ui') && $container->getParameter('api_platform.enable_swagger_ui')) {
            $loader->load('integrations/swagger.xml');
        }

        if (!$container->hasDefinition('api_platform.http_cache.purger.varnish')) {
            return;
        }

        $definition = new Definition(VarnishPurger::class);
        $definition->setDecoratedService('api_platform.http_cache.purger.varnish');
        $definition->addArgument(new Reference(VarnishPurger::class . '.inner'));
        $definition->addArgument(new Reference('router'));

        $container->setDefinition(VarnishPurger::class, $definition);
    }
}
