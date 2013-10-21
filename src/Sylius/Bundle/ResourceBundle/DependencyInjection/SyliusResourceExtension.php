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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\ResourceServicesFactory;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));
        $loader->load('services.xml');

        if (isset($config['resources'])) {
            $this->createResourceServices($config['resources'], $container);
        }
        if (!$container->hasParameter('sylius.config.classes')) {
            $container->setParameter('sylius.config.classes', array());
        }
    }

    /**
     * Remap class parameters.
     *
     * @param array            $classes
     * @param ContainerBuilder $container
     */
    protected function mapClassParameters(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                $container->setParameter(sprintf('sylius.%s.%s.class', $service === 'form' ? 'form.type' : $service, $model), $class);
            }
        }
    }

    /**
     * Remap validation group parameters.
     *
     * @param array            $validationGroups
     * @param ContainerBuilder $container
     */
    protected function mapValidationGroupParameters(array $validationGroups, ContainerBuilder $container)
    {
        foreach ($validationGroups as $model => $groups) {
            $container->setParameter(sprintf('sylius.validation_group.%s', $model), $groups);
        }
    }

    /**
     * Load bundle driver.
     *
     * @param string                $driver
     * @param XmlFileLoader         $loader
     * @param null|ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    protected function loadDatabaseDriver($driver, XmlFileLoader $loader, ContainerBuilder $container = null)
    {
        $bundle = str_replace(array('Extension', 'DependencyInjection\\'), array('Bundle', ''), get_class($this));
        if (!in_array($driver, call_user_func(array($bundle, 'getSupportedDrivers')))) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported by %s.', $driver, basename($bundle)));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        if (null !== $container) {
            $container->setParameter($this->getAlias().'.driver', $driver);
            $container->setParameter($this->getAlias().'.driver.'.$driver, true);
        }
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    private function createResourceServices(array $configs, ContainerBuilder $container)
    {
        $factory = new ResourceServicesFactory($container);

        foreach ($configs as $name => $config) {
            list($prefix, $resourceName) = explode('.', $name);

            $factory->create($prefix, $resourceName, $config['driver'], $config['classes'], array_key_exists('templates', $config) ? $config['templates'] : null);
        }
    }
}
