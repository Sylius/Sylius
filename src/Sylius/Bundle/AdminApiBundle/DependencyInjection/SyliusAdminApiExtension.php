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

namespace Sylius\Bundle\AdminApiBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusAdminApiExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);

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
                'user_provider' => 'sylius.admin_user_provider.email_or_name_based',
                'client_manager' => 'sylius.oauth_server.client_manager',
            ],
        ]);
    }

    private function prependDoctrineMigrations(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine_migrations') || !$container->hasExtension('sylius_labs_doctrine_migrations_extra')) {
            return;
        }

        $doctrineConfig = $container->getExtensionConfig('doctrine_migrations');
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => \array_merge(\array_pop($doctrineConfig)['migrations_paths'] ?? [], [
                'Sylius\Bundle\AdminApiBundle\Migrations' => '@SyliusAdminApiBundle/Migrations',
            ]),
        ]);

        $container->prependExtensionConfig('sylius_labs_doctrine_migrations_extra', [
            'migrations' => [
                'Sylius\Bundle\AdminApiBundle\Migrations' => ['Sylius\Bundle\CoreBundle\Migrations'],
            ],
        ]);
    }
}
