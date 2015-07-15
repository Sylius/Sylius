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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Rbac extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusRbacExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    protected $configFiles = array(
        'services.xml',
        'templating.xml',
        'twig.xml',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        $container->setAlias('sylius.authorization_identity_provider', $config['identity_provider']);
        $container->setAlias('sylius.permission_map', $config['permission_map']);
        $container->setAlias('sylius.authorization_checker', $config['authorization_checker']);

        $container->setParameter('sylius.rbac.security_roles', $config['security_roles']);

        $container->setParameter('sylius.rbac.default_roles', $config['roles']);
        $container->setParameter('sylius.rbac.default_roles_hierarchy', $config['roles_hierarchy']);

        $container->setParameter('sylius.rbac.default_permissions', $config['permissions']);
        $container->setParameter('sylius.rbac.default_permissions_hierarchy', $config['permissions_hierarchy']);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('doctrine_cache')) {
            throw new \RuntimeException('DoctrineCacheBundle must be registered!');
        }

        $container->prependExtensionConfig('doctrine_cache', array(
            'providers' => array(
                'sylius_rbac' => '%sylius.cache%',
            ),
        ));
    }
}
