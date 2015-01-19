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
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Rbac extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusRbacExtension extends AbstractResourceExtension
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

        $configFiles = array(
            'services.xml',
            'templating.xml',
            'twig.xml',
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setAlias('sylius.authorization_identity_provider', $config['identity_provider']);
        $container->setAlias('sylius.permission_map', $config['permission_map']);
        $container->setAlias('sylius.authorization_checker', $config['authorization_checker']);

        $container->setParameter('sylius.rbac.security_roles', $config['security_roles']);

        $container->setParameter('sylius.rbac.default_roles', $config['roles']);
        $container->setParameter('sylius.rbac.default_roles_hierarchy', $config['roles_hierarchy']);

        $container->setParameter('sylius.rbac.default_permissions', $config['permissions']);
        $container->setParameter('sylius.rbac.default_permissions_hierarchy', $config['permissions_hierarchy']);
    }
}
