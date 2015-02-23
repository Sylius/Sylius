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
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ClassMapperExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\DoctrineMongoDBExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\DoctrineOrmExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ExtensionInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\RegisterControllerExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\RegisterFormTypeExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\TargetResolverExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ValidationGroupMapperExtension;
use Sylius\Component\Resource\Exception\Driver\InvalidDriverException;
use Symfony\Cmf\Bundle\CreateBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Sylius\Bundle\TranslationBundle\DependencyInjection\AbstractTranslationExtension;

/**
 * Base extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractResourceExtension extends AbstractTranslationExtension implements PrependExtensionInterface
{
    const CONFIGURE_DATABASE = 2;
    const CONFIGURE_PARAMETERS = 4;
    const CONFIGURE_VALIDATORS = 8;
    const CONFIGURE_FORMS = 16;
    const CONFIGURE_TARGET_RESOLVER = 32;

    const CONFIG_XML = 'xml';
    const CONFIG_YAML = 'yaml';
    const DEFAULT_KEY = 'default';

    protected $applicationName = 'sylius';
    protected $configure = null;
    protected $configDirectory = '/../Resources/config';
    protected $configFormat = self::CONFIG_XML;
    protected $configFiles  = array('services',);

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        // Process configuration
        $processor = new Processor();
        $configuration = $processor->processConfiguration(new Configuration(), $config);
        $configuration = $this->process($configuration, $container);

        // Service defintion
        $loader = $this->createLoader($container);
        $this->loadServiceDefinitions($loader, $this->configFiles);

        // Context
        $context = array(
            'bundle_name' => $this->getAlias(),
            'app_name' => $this->applicationName,
            'loader' => $loader,
            'configure' => $this->configure,
            'application_name' => $this->applicationName,
        );

        // Apply extensions
        foreach ($this->registerExtension() as $extension) {
            if ($extension->isSupported($this->configure)){
                $extension->configure($container, $configuration, $context);
            }
        }

        $classes = isset($config['classes']) ? $config['classes'] : array();
        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
    }

    /**
     * Register the extension used to configura your bundle
     *
     * @return ExtensionInterface[]
     */
    protected function registerExtension()
    {
        // TODO : You should manage priority
        return array(
            new RegisterControllerExtension(),
            new RegisterFormTypeExtension(),
            new ClassMapperExtension(),
            new ValidationGroupMapperExtension(),
            new TargetResolverExtension(),
        );
    }

    /**
     * Create a service definition loader
     *
     * @param ContainerBuilder $container
     *
     * @return LoaderInterface
     */
    protected function createLoader(ContainerBuilder $container)
    {
        $loaderClassName = sprintf('Symfony\Component\DependencyInjection\Loader\%sFileLoader', $this->configFormat);

        if (!class_exists($loaderClassName)) {
            throw new InvalidConfigurationException("The 'configFormat' value is invalid, must be 'xml' or 'yaml'.");
        }

        return new $loaderClassName($container, $this->getConfigurationDirectory());
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
     * Load services deinitions files
     *
     * @param LoaderInterface $loader
     * @param array           $serviceDefinitions
     */
    protected function loadServiceDefinitions(LoaderInterface $loader, $serviceDefinitions)
    {
        if (!is_array($serviceDefinitions)) {
            $serviceDefinitions = array($serviceDefinitions);
        }

        foreach ($serviceDefinitions as $filename) {
            if (file_exists($file = sprintf('%s/%s.%s', $this->getConfigurationDirectory(), $filename, $this->configFormat))) {
                $loader->load($file);
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
     * TODO : Should be remove.
     *
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
}
