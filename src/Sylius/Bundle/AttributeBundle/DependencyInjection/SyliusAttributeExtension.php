<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SyliusAttributeExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);
    }

    /**
     * @param array $resources
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $container->setParameter('sylius.attribute.subjects', $resources);

        $resolvedResources = [];
        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName.'_'.$resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }
}
