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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DatabaseDriverFactoryInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DoctrineODMFactory;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DoctrineORMFactory;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\ResourceServicesFactory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
abstract class BaseExtension extends Extension
{
    const CONFIGURE_LOADER     = 1;
    const CONFIGURE_DATABASE   = 2;
    const CONFIGURE_PARAMETERS = 4;
    const CONFIGURE_VALIDATORS = 8;

    protected $configDir;
    protected $configFiles = array(
        'services',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config/container';

        list($config) = $this->configure($config, new Configuration(), $container);
    }

    /**
     * @param array                  $config
     * @param ConfigurationInterface $configuration
     * @param ContainerBuilder       $container
     * @param mixed                  $configure
     *
     * @return array
     */
    public function configure(array $config, ConfigurationInterface $configuration, ContainerBuilder $container, $configure = self::CONFIGURE_LOADER)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator($this->configDir));

        foreach ($this->configFiles as $filename) {
            $loader->load($filename.'.xml');
        }

        if ($configure & self::CONFIGURE_DATABASE) {
            $this->loadDatabaseDriver($config, $loader, $container);
        }

        $classes = isset($config['classes']) ? $config['classes'] : array();

        if ($configure & self::CONFIGURE_PARAMETERS) {
            $this->mapClassParameters($classes, $container);
        }

        if ($configure & self::CONFIGURE_VALIDATORS) {
            $this->mapValidationGroupParameters($config['validation_groups'], $container);
        }

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);

        return array($config, $loader);
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
     * @param array                 $config
     * @param XmlFileLoader         $loader
     * @param null|ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    protected function loadDatabaseDriver(array $config, XmlFileLoader $loader, ContainerBuilder $container = null)
    {
        $bundle = str_replace(array('Extension', 'DependencyInjection\\'), array('Bundle', ''), get_class($this));
        $driver = $config['driver'];

        if (!in_array($driver, call_user_func(array($bundle, 'getSupportedDrivers')))) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported by %s.', $driver, basename($bundle)));
        }

        $file = sprintf('driver/%s.xml', $driver);

        if (file_exists($this->configDir.'/'.$file)) {
            $loader->load($file);
        }

        $container->setParameter($this->getAlias().'.driver', $driver);
        $container->setParameter($this->getAlias().'.driver.'.$driver, true);

        $factory = $this->getFactoryForDriver($driver, $container);

        foreach ($config['classes'] as $model => $classes) {
            if (array_key_exists('model', $classes)) {
                $factory->create('sylius', $model, $classes);
            }
        }
    }

    /**
     * @param $driver
     *
     * @return DatabaseDriverFactoryInterface
     */
    private function getFactoryForDriver($driver, ContainerBuilder $container)
    {
        if ('doctrine/orm' === $driver) {
            return new DoctrineORMFactory($container);
        }
    }
}
