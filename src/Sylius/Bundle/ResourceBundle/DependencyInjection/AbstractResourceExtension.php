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

use Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Sylius\Component\Resource\Exception\Driver\InvalidDriverException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractResourceExtension extends Extension
{
    const CONFIGURE_LOADER       = 1;
    const CONFIGURE_DATABASE     = 2;
    const CONFIGURE_PARAMETERS   = 4;
    const CONFIGURE_VALIDATORS   = 8;
    const CONFIGURE_FORMS        = 16;
    const CONFIGURE_TRANSLATIONS = 32;

    const CONFIG_XML  = 'xml';
    const CONFIG_YAML = 'yml';

    protected $applicationName = 'sylius';
    protected $configDirectory = '/../Resources/config';

    /**
     * Configure the file formats of the files loaded using $configFiles variable.
     *
     * @var string
     */
    protected $configFormat = self::CONFIG_XML;

    protected $configFiles  = array(
        'services',
    );

    const DEFAULT_KEY = 'default';

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
        $config = $processor->processConfiguration($configuration, $config);

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

        if ($this->isTranslationSupported() && $configure & self::CONFIGURE_TRANSLATIONS) {
            $this->configureTranslations($config, $container);
        }

        $classes = isset($config['classes']) ? $config['classes'] : array();

        if ($configure & self::CONFIGURE_PARAMETERS) {
            $this->mapClassParameters($classes, $container);
        }

        if ($configure & self::CONFIGURE_VALIDATORS) {
            $this->mapValidationGroupParameters($config['validation_groups'], $container);
        }

        if ($configure & self::CONFIGURE_FORMS) {
            $this->registerFormTypes($config, $container);
        }

        $configClasses = array();

        if ($container->hasParameter('sylius.config.classes')) {
            $configClasses = array_merge_recursive(
                array($this->applicationName => $classes),
                $container->getParameter('sylius.config.classes')
            );
        }

        $container->setParameter('sylius.config.classes', $configClasses);

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
                if ('form' === $service) {
                    if (!is_array($class)) {
                        $class = array(self::DEFAULT_KEY => $class);
                    }
                    foreach ($class as $suffix => $subClass) {
                        $container->setParameter(
                            sprintf(
                                '%s.form.type.%s%s.class',
                                $this->applicationName,
                                $model,
                                $suffix === self::DEFAULT_KEY ? '' : sprintf('_%s', $suffix)
                            ),
                            $subClass
                        );
                    }
                } elseif ('translation' === $service) {
                    $this->mapClassParameters(array(sprintf('%s_translation', $model) => $class), $container);
                } else {
                    $container->setParameter(
                        sprintf(
                            '%s.%s.%s.class',
                            $this->applicationName,
                            $service,
                            $model
                        ),
                        $class
                    );
                }
            }
        }
    }

    /**
     * Register resource form types
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function registerFormTypes(array $config, ContainerBuilder $container)
    {
        foreach ($config['classes'] as $model => $serviceClasses) {
            if (!isset($serviceClasses['form']) || !is_array($serviceClasses['form'])) {
                continue;
            }

            if ($this->isTranslationSupported() && isset($serviceClasses['translation'])) {
                $this->registerFormTypes(array('classes' => array(sprintf('%s_translation', $model) => $serviceClasses['translation'])), $container);
            }

            foreach ($serviceClasses['form'] as $name => $class) {
                $suffix = ($name === self::DEFAULT_KEY ? '' : sprintf('_%s', $name));
                $alias = sprintf('%s_%s%s', $this->applicationName, $model, $suffix);
                $definition = new Definition($class);
                if ('choice' === $name) {
                    $definition->setArguments(array(
                        $serviceClasses['model'],
                        $config['driver'],
                        $alias,
                    ));
                } else {
                    $definition->setArguments(array(
                        $serviceClasses['model'],
                        new Parameter(sprintf('%s.validation_group.%s%s', $this->applicationName, $model, $suffix)),
                    ));
                }
                $definition->addTag('form.type', array('alias' => $alias));
                $container->setDefinition(
                    sprintf('%s.form.type.%s%s', $this->applicationName, $model, $suffix),
                    $definition
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
        $manager = isset($config['object_manager']) ? $config['object_manager'] : 'default';

        if (!in_array($driver, call_user_func(array($bundle, 'getSupportedDrivers')))) {
            throw new InvalidDriverException($driver, basename($bundle));
        }

        $this->loadConfigurationFile(array(sprintf('driver/%s', $driver)), $loader);

        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.object_manager', $this->getAlias()), $manager);

        if (!isset($config['classes'])) {
            return;
        }

        foreach ($config['classes'] as $model => $classes) {
            if (array_key_exists('model', $classes)) {
                DatabaseDriverFactory::get(
                    $container,
                    $this->applicationName,
                    $model,
                    $manager,
                    $driver,
                    isset($config['templates'][$model]) ? $config['templates'][$model] : null
                )->load($classes);
            }
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureTranslations(array $config, ContainerBuilder $container)
    {
        $driver = $config['driver'];
        $manager = isset($config['object_manager']) ? $config['object_manager'] : 'default';
        $mapper = new Mapper();

        foreach ($config['classes'] as $model => $classes) {
            if (array_key_exists('model', $classes) && array_key_exists('translation', $classes)) {
                $mapper->mapTranslations($classes, $container);

                DatabaseDriverFactory::get(
                    $container,
                    $this->applicationName,
                    sprintf('%s_translation', $model),
                    $manager,
                    $driver,
                    isset($config['templates'][$model]) ? $config['templates'][$model] : null
                )->load($classes['translation']);
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

        if (!is_dir($directory = dirname($fileName).$this->configDirectory)) {
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

    /**
     * Are translations supported in this app?
     *
     * @return bool
     */
    private function isTranslationSupported()
    {
        return class_exists('Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper');
    }
}
