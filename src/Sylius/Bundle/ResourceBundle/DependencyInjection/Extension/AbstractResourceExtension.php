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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Sylius\Bundle\TranslationBundle\DependencyInjection\Mapper;
use Sylius\Component\Resource\Exception\Driver\InvalidDriverException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * Base extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractResourceExtension extends AbstractExtension
{
    const CONFIGURE_LOADER       = 1;
    const CONFIGURE_DATABASE     = 2;
    const CONFIGURE_PARAMETERS   = 4;
    const CONFIGURE_VALIDATORS   = 8;
    const CONFIGURE_FORMS        = 16;
    const CONFIGURE_TRANSLATIONS = 32;

    const DEFAULT_KEY = 'default';

    protected $applicationName = 'sylius';
    protected $configFiles = array('services.xml');

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

        $this->loadServiceDefinitions($container, $this->configFiles);

        if ($configure & self::CONFIGURE_DATABASE) {
            $this->loadDatabaseDriver($config, $container);
        }

        if ($this->isTranslationSupported() && $configure & self::CONFIGURE_TRANSLATIONS) {
            $this->configureTranslations($config, $container);
        }

        $resources = isset($config['resources']) ? $config['resources'] : array();

        if ($configure & self::CONFIGURE_PARAMETERS) {
            $this->mapClassParameters($resources, $container);
        }

        if ($configure & self::CONFIGURE_VALIDATORS) {
            $this->mapValidationGroupParameters($config['resources'], $container);
            $this->mapFormsValidationGroupParameters($config, $container);
        }

        if ($configure & self::CONFIGURE_FORMS) {
            $this->registerFormTypes($config, $container);
        }

        $configClasses = array($this->applicationName => $resources);

        if ($container->hasParameter('sylius.config.classes')) {
            $configClasses = array_merge_recursive(
                $configClasses,
                $container->getParameter('sylius.config.classes')
            );
        }

        $container->setParameter('sylius.config.classes', $configClasses);

        return $config;
    }

    /**
     * Remap class parameters.
     *
     * @param array            $resources
     * @param ContainerBuilder $container
     */
    protected function mapClassParameters(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $resource => $parameters) {
            if (isset($parameters['translation'])) {
                $this->mapClassParameters(array(sprintf('%s_translation', $resource) => $parameters['translation']), $container);
            }

            foreach ($parameters['classes'] as $serviceName => $serviceClassOrClasses) {
                if (!is_array($serviceClassOrClasses)) {
                    $container->setParameter(sprintf('%s.%s.%s.class', $this->applicationName, $serviceName, $resource), $serviceClassOrClasses);
                } else {
                    $serviceClasses = $serviceClassOrClasses;

                    foreach ($serviceClasses as $serviceType => $serviceClass) {
                        if (self::DEFAULT_KEY === $serviceType) {
                            $container->setParameter(sprintf('%s.%s.%s.class', $this->applicationName, $serviceName, $resource), $serviceClass);
                        } else {
                            $container->setParameter(sprintf('%s.%s.%s_%s.class', $this->applicationName, $serviceName, $resource, $serviceType), $serviceClass);
                        }
                    }
                }
            }
        }
    }

    /**
     * Register resource form types.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function registerFormTypes(array $config, ContainerBuilder $container)
    {
        foreach ($config['resources'] as $resource => $parameters) {
            if (!isset($parameters['classes']['form']) || !is_array($parameters['classes']['form'])) {
                continue;
            }

            if ($this->isTranslationSupported() && isset($parameters['translation'])) {
                $this->registerFormTypes(array('resources' => array(sprintf('%s_translation', $resource) => $parameters['translation'])), $container);
            }

            foreach ($parameters['classes']['form'] as $name => $class) {
                $suffix = ($name === self::DEFAULT_KEY ? '' : sprintf('_%s', $name));
                $alias = sprintf('%s_%s%s', $this->applicationName, $resource, $suffix);
                $definition = new Definition($class);
                if ('choice' === $name) {
                    $definition->setArguments(array(
                        $parameters['classes']['model'],
                        $config['driver'],
                        $alias,
                    ));
                } else {
                    $validationGroupsParameterName = sprintf('%s.validation_group.%s%s', $this->applicationName, $resource, $suffix);
                    $validationGroups = array('Default');

                    if ($container->hasParameter($validationGroupsParameterName)) {
                        $validationGroups = new Parameter($validationGroupsParameterName);
                    }

                    $definition->setArguments(array(
                        $parameters['classes']['model'],
                        $validationGroups,
                    ));
                }
                $definition->addTag('form.type', array('alias' => $alias));
                $container->setDefinition(
                    sprintf('%s.form.type.%s%s', $this->applicationName, $resource, $suffix),
                    $definition
                );
            }
        }
    }

    /**
     * Remap validation group parameters.
     *
     * @param array            $resources
     * @param ContainerBuilder $container
     */
    protected function mapValidationGroupParameters(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $resource => $parameters) {
            if (isset($parameters['validation_groups'])) {
                $container->setParameter(sprintf('%s.validation_group.%s', $this->applicationName, $resource), $parameters['validation_groups']);
            }
        }
    }

    /**
     * Remap validation group parameters for forms.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function mapFormsValidationGroupParameters(array $config, ContainerBuilder $container)
    {
        if (isset($config['validation_groups'])) {
            foreach ($config['validation_groups'] as $validationGroups => $class) {
                $container->setParameter(sprintf('%s.validation_group.%s', $this->applicationName, $validationGroups), $validationGroups);
            }
        }
    }

    /**
     * Load bundle driver.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @throws InvalidDriverException
     */
    protected function loadDatabaseDriver(array $config, ContainerBuilder $container)
    {
        $bundle = str_replace(array('Extension', 'DependencyInjection\\'), array('Bundle', ''), get_class($this));
        $driver = $config['driver'];
        $manager = isset($config['object_manager']) ? $config['object_manager'] : 'default';

        if (!in_array($driver, call_user_func(array($bundle, 'getSupportedDrivers')))) {
            throw new InvalidDriverException($driver, basename($bundle));
        }

        $this->loadDriverDefinition($container, $driver);

        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.object_manager', $this->getAlias()), $manager);

        if (!isset($config['resources'])) {
            return;
        }

        foreach ($config['resources'] as $resource => $parameters) {
            if (array_key_exists('model', $parameters['classes'])) {
                DatabaseDriverFactory::get(
                    $container,
                    $this->applicationName,
                    $resource,
                    $manager,
                    $driver,
                    isset($config['templates'][$resource]) ? $config['templates'][$resource] : null
                )->load($parameters);
            }
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configureTranslations(array $config, ContainerBuilder $container)
    {
        $driver = $config['driver'];
        $manager = isset($config['object_manager']) ? $config['object_manager'] : 'default';
        $mapper = new Mapper();

        foreach ($config['resources'] as $resource => $parameters) {
            if (isset($parameters['classes']['model']) && isset($parameters['translation']['classes']['model'])) {
                $mapper->mapTranslations($parameters, $container);
                $this->mapValidationGroupParameters($parameters['translation'], $container);

                DatabaseDriverFactory::get(
                    $container,
                    $this->applicationName,
                    sprintf('%s_translation', $resource),
                    $manager,
                    $driver,
                    isset($config['templates'][$resource]) ? $config['templates'][$resource] : null
                )->load($parameters['translation']);
            }
        }
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
