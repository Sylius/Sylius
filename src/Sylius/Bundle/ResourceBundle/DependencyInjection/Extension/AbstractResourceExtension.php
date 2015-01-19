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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Sylius\Component\Resource\Metadata\ResourceMetadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base resource extension for Sylius bundles.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
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

        foreach ($resources as $resourceName => $resourceConfig) {
            $alias = $applicationName.'.'.$resourceName;
            $resourceConfig = array_merge(array('driver' => $driver), $resourceConfig);

            $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : array();
            $resources = array_merge($resources, array($alias => $resourceConfig));

            $container->setParameter('sylius.resources', $resources);

            $metadata = ResourceMetadata::fromConfigurationArray($alias, $resourceConfig);

            DatabaseDriverFactory::getForResource($metadata)->load($container, $metadata);

            if ($metadata->isTranslatable()) {
                $alias = $alias.'_translation';
                $resourceConfig = array_merge(array('driver' => $driver), $resourceConfig['translation']);

                $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : array();
                $resources = array_merge($resources, array($alias => $resourceConfig));

                $container->setParameter('sylius.resources', $resources);

                $metadata = ResourceMetadata::fromConfigurationArray($alias, $resourceConfig);

                DatabaseDriverFactory::getForResource($metadata)->load($container, $metadata);
            }
        }
    }
}
