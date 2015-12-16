<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractExtension;
use Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusResourceExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $config);

        $this->loadServiceDefinitions($container, array(
            'services.xml',
            'storage.xml',
            'routing.xml',
            'twig.xml',
        ));

        $resources = isset($config['resources']) ? $config['resources'] : array();

        $container->setParameter('sylius.resource.settings', $config['settings']);

        foreach ($resources as $resource)
        $this->createResourceServices($resources, $container);

        $configClasses = array('default' => $this->getClassesFromConfig($resources));

        if ($container->hasParameter('sylius.config.classes')) {
            $configClasses = array_merge_recursive(
                $configClasses,
                $container->getParameter('sylius.config.classes')
            );
        }

        $container->setParameter('sylius.config.classes', $configClasses);
    }

    /**
     * @param array            $resource
     * @param ContainerBuilder $container
     */
    private function createResourceServices(array $resource, ContainerBuilder $container)
    {
        $translationsEnabled = class_exists('Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper');

        if ($translationsEnabled) {
            $mapper = new Mapper();
        }

        foreach ($resource as $name => $config) {
            list($prefix, $resourceName) = explode('.', $name);
            $manager = isset($config['object_manager']) ? $config['object_manager'] : 'default';

            DatabaseDriverFactory::get(
                $container,
                $prefix,
                $resourceName,
                $manager,
                $config['driver'],
                array_key_exists('templates', $config) ? $config['templates'] : null
            )->load($config['classes']);

            if ($translationsEnabled && array_key_exists('model', $config['classes']) && array_key_exists('translation', $config['classes'])) {
                $mapper->mapTranslations($config['classes'], $container);

                DatabaseDriverFactory::get(
                    $container,
                    $prefix,
                    sprintf('%s_translation', $resourceName),
                    $manager,
                    $config['driver']
                )->load($config['translation']);
            }
        }
    }

    /**
     * @param array $configs
     * @return array
     */
    private function getClassesFromConfig($configs)
    {
        $classes = array();

        foreach ($configs as $config) {
            $classes[] = $config['classes'];
        }

        return $classes;
    }
}
