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

namespace Sylius\Bundle\ChannelBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Channel\Attribute\AsChannelContext;
use Sylius\Component\Channel\Attribute\AsChannelContextRequestResolver;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
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

        $container->registerForAutoconfiguration(ChannelContextInterface::class)
            ->addTag('sylius.context.channel')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsChannelContext::class,
            static function (ChildDefinition $definition, AsChannelContext $attribute) {
                $definition->addTag('sylius.context.channel', [
                    'priority' => $attribute->priority,
                ]
            );
        });

        $container->registerForAutoconfiguration(RequestResolverInterface::class)
            ->addTag('sylius.context.channel.request_based.resolver')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsChannelContextRequestResolver::class,
            static function (ChildDefinition $definition, AsChannelContextRequestResolver $attribute) {
                $definition->addTag('sylius.context.channel.request_based.resolver', [
                    'priority' => $attribute->priority,
                ]);
            }
        );
    }
}
