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
use Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('storage.xml');
        $loader->load('routing.xml');
        $loader->load('twig.xml');

        $classes = isset($config['resources']) ? $config['resources'] : array();

        $container->setParameter('sylius.resource.settings', $config['settings']);

        $this->createResourceServices($classes, $container);

        $configClasses = array();

        if ($container->hasParameter('sylius.config.classes')) {
            $configClasses = array_merge_recursive(
                array('default' => $classes),
                $container->getParameter('sylius.config.classes')
            );
        }

        $container->setParameter('sylius.config.classes', $configClasses);
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    private function createResourceServices(array $configs, ContainerBuilder $container)
    {
        $translationsEnabled = class_exists('Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper');

        if ($translationsEnabled) {
            $mapper = new Mapper();
        }

        foreach ($configs as $name => $config) {
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
                )->load($config['classes']['translation']);
            }
        }
    }
}
