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

namespace Sylius\Bundle\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('sylius.admin.notification.enabled', $config['notifications']['enabled']);
        $container->setParameter('sylius.admin.notification.frequency', $config['notifications']['frequency']);
        $container->setParameter('sylius.admin.shop_enabled', false);

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('SyliusShopBundle', $bundles)) {
            $loader->load('services/integrations/shop.xml');
            $container->setParameter('sylius.admin.shop_enabled', true);
        }

        $loader->load('services.xml');
    }
}
