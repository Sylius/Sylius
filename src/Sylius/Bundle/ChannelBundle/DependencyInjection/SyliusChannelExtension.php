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

namespace Sylius\Bundle\ChannelBundle\DependencyInjection;

use Sylius\Bundle\ChannelBundle\Attribute\AsChannelContext;
use Sylius\Bundle\ChannelBundle\Attribute\AsRequestBasedChannelResolver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusChannelExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        if ($config['debug'] ?? $container->getParameter('kernel.debug')) {
            $loader->load('services/integrations/debug.xml');

            $container->getDefinition('sylius.channel_collector')->replaceArgument(2, true);
        }

        $container->getDefinition('sylius.repository.channel')->setLazy(true);

        $this->registerAutoconfiguration($container);
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsChannelContext::class,
            static function (ChildDefinition $definition, AsChannelContext $attribute): void {
                $definition->addTag(AsChannelContext::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsRequestBasedChannelResolver::class,
            static function (ChildDefinition $definition, AsRequestBasedChannelResolver $attribute): void {
                $definition->addTag(AsRequestBasedChannelResolver::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );
    }
}
