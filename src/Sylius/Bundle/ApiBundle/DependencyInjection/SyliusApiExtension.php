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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Api extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusApiExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }
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
        $resourcesConfig = $config['resources'];

        $container->prependExtensionConfig('fos_oauth_server', [
            'db_driver' => 'orm',
            'client_class' => $resourcesConfig['api_client']['classes']['model'],
            'access_token_class' => $resourcesConfig['api_access_token']['classes']['model'],
            'refresh_token_class' => $resourcesConfig['api_refresh_token']['classes']['model'],
            'auth_code_class' => $resourcesConfig['api_auth_code']['classes']['model'],

            'service' => [
                'user_provider' => 'sylius.user_provider.name_or_email',
                'client_manager' => 'sylius.oauth_server.client_manager',
            ],
        ]);
    }
}
