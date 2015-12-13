<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DriverFactory;
use Sylius\Component\Resource\Metadata\Metadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class AbstractResourceExtension extends Extension
{
    /**
     * @param string $applicationName
     * @param string $driver
     * @param array $resources
     * @param ContainerBuilder $container
     */
    protected function registerResources($applicationName, $driver, array $resources, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);

        foreach ($resources as $resourceName => $resourceConfig) {
            $alias = $applicationName.'.'.$resourceName;
            $resourceConfig = array_merge(array('driver' => $driver), $resourceConfig);

            $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : array();
            $resources = array_merge($resources, array($alias => $resourceConfig));
            $container->setParameter('sylius.resources', $resources);

            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            DriverFactory::get($metadata)->load($container, $metadata);

            if ($metadata->hasParameter('translation')) {
                $alias = $alias.'_translation';
                $resourceConfig = array_merge(array('driver' => $driver), $resourceConfig['translation']);

                $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : array();
                $resources = array_merge($resources, array($alias => $resourceConfig));
                $container->setParameter('sylius.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverFactory::get($metadata)->load($container, $metadata);
            }
        }
    }


    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function mapFormValidationGroupsParameters(array $config, ContainerBuilder $container)
    {
        if (isset($config['validation_groups'])) {
            foreach ($config['validation_groups'] as $name => $groups) {
                $container->setParameter(sprintf('sylius.validation_groups.%s', $name), $groups);
            }
        }
    }
}
