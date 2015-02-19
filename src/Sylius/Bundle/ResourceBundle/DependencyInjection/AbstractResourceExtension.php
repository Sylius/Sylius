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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractResourceExtension extends Extension implements PrependExtensionInterface
{
    const CONFIGURE_LOADER = 1;

    const CONFIGURE_DATABASE = 2;

    const CONFIGURE_PARAMETERS = 4;

    const CONFIGURE_VALIDATORS = 8;

    const CONFIGURE_FORMS = 16;

    const CONFIG_XML = 'xml';

    const CONFIG_YAML = 'yml';

    protected $applicationName = 'sylius';

    protected $configDirectory = '/../Resources/config';

    /**
     * Configure the file formats of the files loaded using $configFiles variable.
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

        $classes = isset($config['classes']) ? $config['classes'] : array();

        $this->mapTranslations($classes, $container);

        if ($configure & self::CONFIGURE_PARAMETERS) {
            $this->mapClassParameters($classes, $container);
        }

        if ($configure & self::CONFIGURE_VALIDATORS) {
            $this->mapValidationGroupParameters($config['validation_groups'], $container);
        }

        if ($configure & self::CONFIGURE_FORMS) {
            $this->registerFormTypes($config, $container);
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
     * @param array            $classes
     * @param ContainerBuilder $container
     */
    protected function mapTranslations(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $class) {
            if (array_key_exists('translation', $class) || array_key_exists('translatable', $class)) {
                $this->processTranslations($class, $container);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_resource')) {
            throw new ServiceNotFoundException('SyliusResourceBundle must be registered in kernel.');
        }

        // If the default mapping parameter has already been defined we don't need to do anything
        if ($container->hasParameter('sylius.translation.default.mapping')) {
            return;
        }

        // Parse sylius_translation to get the default mapping values and assign them to
        // 'sylius.translation.default.mapping' parameter to be used un process) method.
        $configs = $container->getExtensionConfig('sylius_resource');
        $config  = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('sylius.translation.default.mapping', $config['default_mapping']);
    }

    /**
     * In case any extra processing is needed.
     *
     * @param array            $class
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    protected function processTranslations(array $class, ContainerBuilder $container)
    {
        if (!($container->hasParameter('sylius.translation.mapping')
            && $translationsMapping = $container->getParameter('sylius.translation.mapping'))
        ) {
            $translationsMapping = array();
        }

        if (!($container->hasParameter('sylius.translation.default.mapping')
            && $defaultValues = $container->getParameter('sylius.translation.default.mapping'))
        ) {
            throw new \Exception('Missing parameter sylius.translation.default.mapping. Default translation mapping must be defined!');
        }

        if (isset($class['translatable'])) {
            $translationsMapping = $this->mapTranslatable(
                $translationsMapping,
                $class['model'],
                $class['translatable'],
                $defaultValues
            );
        } elseif (isset($class['translation'])) {
            $translationsMapping = $this->mapTranslation(
                $translationsMapping,
                $class['model'],
                $class['translation']
            );
        }

        $container->setParameter('sylius.translation.mapping', $translationsMapping);
    }

    /**
     * Set translatable entity mapping metadata
     *
     * @param array  $translationsMapping
     * @param string $translatableClass
     * @param array  $translatableConfig
     * @param array  $defaultValues
     *
     * @return array
     */
    protected function mapTranslatable(array $translationsMapping, $translatableClass, array $translatableConfig, array $defaultValues)
    {
        // Map translatable target entity
        $translationClass = isset($translatableConfig['targetEntity']) ? $translatableConfig['targetEntity'] : $translatableClass.'Translation';
        $translatableConfig['targetEntity'] = $translationClass;

        $translationMetadata = array_merge($defaultValues['translatable'], $translatableConfig);

        $translationsMapping[$translatableClass] = $translationMetadata;

        // Mapping for translation entity with default values
        $translationMetadata  = array_merge($defaultValues['translation'], array('targetEntity' => $translatableClass));
        $translationsMapping[$translationClass] = $translationMetadata;

        return $translationsMapping;
    }

    /**
     * Set translation entity mapping metadata
     *
     * @param array  $translationMapping
     * @param string $translation
     * @param array  $translationConfig
     *
     * @return array
     */
    protected function mapTranslation(array $translationMapping, $translation, array $translationConfig)
    {
        // At this point we already have the default values mapped, so we only need to override them
        // if specific values have been set in entity_translation configuration key
        if (isset($translationConfig['translatable'])) {
            $translationMapping[$translation]['field'] = $translationConfig['translatable'];
        }

        if (isset($translationConfig['locale'])) {
            $translationMapping[$translation]['locale'] = $translationConfig['locale'];
        }

        return $translationMapping;
    }
}
