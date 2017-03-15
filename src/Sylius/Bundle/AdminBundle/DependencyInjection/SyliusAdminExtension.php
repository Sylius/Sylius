<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\DependencyInjection;

use Sylius\Bundle\AdminBundle\Twig\NotificationExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SyliusAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->configureNotifications($config['notification'], $container);

        $loader->load('services.xml');
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function configureNotifications(array $config, ContainerBuilder $container)
    {
        $container->setParameter('sylius.admin.notification.enabled', $config['enabled']);

        $notificationExtension = new Definition(NotificationExtension::class);
        $notificationExtension->addArgument($config['enabled']);
        $notificationExtension->addArgument($config['frequency'] ?: null);
        $notificationExtension->addTag('twig.extension');

        $container->setDefinition('sylius.notification.extension', $notificationExtension);
    }
}
