<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Api extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusApiExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    protected $configFiles = array(
        'services.xml'
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ServiceNotFoundException
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('fos_oauth_server')) {
            throw new ServiceNotFoundException('FOSOAuthServerBundle must be registered in kernel.');
        }

        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $container->prependExtensionConfig('fos_oauth_server', array(
            'db_driver'           => 'orm',
            'client_class'        => $config['resources']['api_client']['classes']['model'],
            'access_token_class'  => $config['resources']['api_access_token']['classes']['model'],
            'refresh_token_class' => $config['resources']['api_refresh_token']['classes']['model'],
            'auth_code_class'     => $config['resources']['api_auth_code']['classes']['model'],

            'service'             => array(
                'user_provider'  => 'sylius.user_provider.name_or_email',
                'client_manager' => 'sylius.oauth_server.client_manager',
            ),
        ));
    }
}
