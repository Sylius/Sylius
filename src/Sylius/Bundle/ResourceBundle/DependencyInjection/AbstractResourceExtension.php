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
use Sylius\Component\Resource\Exception\Driver\InvalidDriverException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
abstract class AbstractResourceExtension extends Extension
{
    const CONFIGURE_LOADER     = 1;
    const CONFIGURE_DATABASE   = 2;
    const CONFIGURE_PARAMETERS = 4;
    const CONFIGURE_VALIDATORS = 8;

    const CONFIG_XML  = 'xml';
    const CONFIG_YAML = 'yml';

    protected $applicationName = 'sylius';
    protected $configDirectory = '/../Resources/config';

    /**
     * Configure the file formats of the files loaded using $configFiles variable.
     * @var string
     */
    protected $configFormat = self::CONFIG_XML;
    protected $configFiles = array(
        'services',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure($config, new Configuration(), $container);
    }

    /**
     * @param array                  $config
     * @param ConfigurationInterface $configuration
     * @param ContainerBuilder       $container
     * @param integer                $configure
     *
     * @return array
     */
    public function configure(
        array $config,
        ConfigurationInterface $configuration,
        ContainerBuilder $container,
        $configure = self::CONFIGURE_LOADER
    ) {
        $processor = new Processor();
        $config    = $processor->processConfiguration($configuration, $config);

        $config = $this->process($config, $container);

        if ($this->configFormat === self::CONFIG_XML) {
            $loader = new XmlFileLoader($container, new FileLocator($this->getConfigurationDirectory()));
        } elseif ($this->configFormat === self::CONFIG_YAML) {
            $loader = new YamlFileLoader($container, new FileLocator($this->getConfigurationDirectory()));
        } else {
            throw new InvalidConfigurationException("The 'configFormat' value is invalid, must be 'xml' or 'yml'.");
        }

        $this->loadConfigurationFile($this->configFiles, $loader);

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
                $container->setParameter(
                    sprintf(
                        '%s.%s.%s.class',
                        $this->applicationName,
                        $service === 'form' ? 'form.type' : $service,
                        $model
                    ),
                    $class
                );
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
            $container->setParameter(sprintf('%s.validation_group.%s', $this->applicationName, $model), $groups);
        }
    }

    /**
     * Load bundle driver.
     *
     * @param array                 $config
     * @param LoaderInterface       $loader
     * @param null|ContainerBuilder $container
     *
     * @throws InvalidDriverException
     */
    protected function loadDatabaseDriver(array $config, LoaderInterface $loader, ContainerBuilder $container)
    {
        $bundle = str_replace(array('Extension', 'DependencyInjection\\'), array('Bundle', ''), get_class($this));
        $driver = $config['driver'];

        if (!in_array($driver, call_user_func(array($bundle, 'getSupportedDrivers')))) {
            throw new InvalidDriverException($driver, basename($bundle));
        }

        $this->loadConfigurationFile(array(sprintf('driver/%s', $driver)), $loader);

        $container->setParameter($this->getAlias().'.driver', $driver);
        $container->setParameter($this->getAlias().'.driver.'.$driver, true);

        foreach ($config['classes'] as $model => $classes) {
            if (array_key_exists('model', $classes)) {
                DatabaseDriverFactory::get(
                    $driver,
                    $container,
                    $this->applicationName,
                    $model,
                    isset($config['object_manager']) ? $config['object_manager'] : 'default',
                    isset($config['templates'][$model]) ? $config['templates'][$model] : null
                )->load($classes);
            }
        }
    }

    /**
     * @param array           $config
     * @param LoaderInterface $loader
     */
    protected function loadConfigurationFile(array $config, LoaderInterface $loader)
    {
        foreach ($config as $filename) {
            if (file_exists($file = sprintf('%s/%s.%s', $this->getConfigurationDirectory(), $filename, $this->configFormat))) {
                $loader->load($file);
            }
        }
    }

    /**
     * Get the configuration directory
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getConfigurationDirectory()
    {
        $reflector = new \ReflectionClass($this);
        $fileName = $reflector->getFileName();

        if (!is_dir($directory = dirname($fileName) . $this->configDirectory)) {
            throw new \RuntimeException(sprintf('The configuration directory "%s" does not exists.', $directory));
        }

        return $directory;
    }

    /**
     * In case any extra processing is needed.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function process(array $config, ContainerBuilder $container)
    {
        // Override if needed.
        return $config;
    }
}
