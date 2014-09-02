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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Api extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusApiExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    protected $configFiles = array(
        'services',
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
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        if (!$container->hasExtension('fos_oauth_server')) {
            throw new \Exception('FOSOAuthServerBundle must be registered in kernel.');
        }

        $container->prependExtensionConfig('fos_oauth_server', array(
            'db_driver'           => 'orm',
            'client_class'        => $config['classes']['api_client']['model'],
            'access_token_class'  => $config['classes']['api_access_token']['model'],
            'refresh_token_class' => $config['classes']['api_refresh_token']['model'],
            'auth_code_class'     => $config['classes']['api_auth_code']['model'],
            'service'             => array('user_provider' => 'fos_user.user_provider')
        ));

        if (!$container->hasExtension('jms_serializer')) {
            throw new \Exception('JMSSerializerBundle must be registered in kernel.');
        }

        $directories = array(
            'sylius-core'     => array('namespace_prefix' => 'Sylius\\Component\\Core', 'path' => '@SyliusCoreBundle/Resources/config/serializer'),
            'sylius-shipping' => array('namespace_prefix' => 'Sylius\\Component\\Shipping', 'path' => '@SyliusShippingBundle/Resources/config/serializer'),
            'sylius-taxation' => array('namespace_prefix' => 'Sylius\\Component\\Taxation', 'path' => '@SyliusTaxationBundle/Resources/config/serializer'),
        );

        $container->prependExtensionConfig('jms_serializer', array(
            'metadata' => array(
                'directories' => $directories
            )
        ));
    }

}
