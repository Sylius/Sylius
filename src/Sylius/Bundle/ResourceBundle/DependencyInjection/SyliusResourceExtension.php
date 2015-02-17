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
use Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusResourceExtension extends AbstractResourceExtension
{
    protected $configFiles  = array(
        'services',
        'storage',
        'routing',
        'twig',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list($config) = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER  | self::CONFIGURE_PARAMETERS
        );

        $classes = isset($config['resources']) ? $config['resources'] : array();

        $container->setParameter('sylius.resource.settings', $config['settings']);

        $this->createResourceServices($classes, $container);

        $this->mapTranslations($classes, $container);

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);

        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $config['driver']);
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
    protected function mapTranslations(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            if (array_key_exists('translation', $config['classes']) || array_key_exists('translatable', $config['classes'])) {
                $this->processTranslations($config['classes'], $container);
            }
        }
    }
}
