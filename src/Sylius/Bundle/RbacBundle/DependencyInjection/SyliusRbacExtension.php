<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusRbacExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
            'templating.xml',
            'twig.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setAlias('sylius.rbac.hierarchy_provider', $config['hierarchy_provider']);

        $container->setParameter('sylius.rbac.default_roles', $config['roles']);
        $container->setParameter('sylius.rbac.default_permissions', $config['permissions']);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependDoctrineCache($container);
        $this->prependSyliusResource($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prependDoctrineCache(ContainerBuilder $container)
    {
        if (!$container->hasExtension('doctrine_cache')) {
            throw new \RuntimeException('DoctrineCacheBundle must be registered!');
        }

        $container->prependExtensionConfig('doctrine_cache', [
            'providers' => [
                'sylius_rbac' => '%sylius.cache%',
            ],
        ]);
    }
    /**
     * @param ContainerBuilder $container
     */
    private function prependSyliusResource(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_resource')) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('resource_integration.xml');

        $container->prependExtensionConfig('sylius_resource', [
            'authorization_checker' => 'sylius.resource_controller.authorization_checker.rbac',
        ]);
    }
}
