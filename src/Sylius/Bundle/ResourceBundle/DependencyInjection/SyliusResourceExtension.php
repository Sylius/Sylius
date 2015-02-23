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
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Sylius\Bundle\TranslationBundle\DependencyInjection\AbstractTranslationExtension;

/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusResourceExtension extends AbstractTranslationExtension
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

        $this->mapTranslations($classes, $container);

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    private function createResourceServices(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $name => $config) {
            list($prefix, $resourceName) = explode('.', $name);

            DatabaseDriverFactory::get(
                $container,
                $prefix,
                $resourceName,
                isset($config['object_manager']) ? $config['object_manager'] : 'default',
                $config['driver'],
                array_key_exists('templates', $config) ? $config['templates'] : null
            )->load($config['classes']);
        }
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    private function mapTranslations(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            if (array_key_exists('translation', $config['classes']) || array_key_exists('translatable', $config['classes'])) {
                $this->processTranslations($config['classes'], $container);
            }
        }
    }
}
