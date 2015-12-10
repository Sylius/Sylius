<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
abstract class AbstractDatabaseDriver implements DatabaseDriverInterface
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $managerName;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var string
     */
    protected $templates;

    public function __construct(ContainerBuilder $container, $prefix, $resourceName, $managerName, $templates = null)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->resourceName = $resourceName;
        $this->managerName = $managerName;
        $this->templates = $templates;
    }

    public function load(array $parameters)
    {
        if (isset($parameters['classes']['controller'])) {
            $this->container->setDefinition(
                $this->getContainerKey('controller'),
                $this->getControllerDefinition($parameters['classes']['controller'])
            );
        }

        $repositoryDefinition = $this->getRepositoryDefinition($parameters);
        $reflection = new \ReflectionClass($repositoryDefinition->getClass());

        $translatableRepositoryInterface = 'Sylius\Component\Translation\Repository\TranslatableResourceRepositoryInterface';

        if (interface_exists($translatableRepositoryInterface) && $reflection->implementsInterface($translatableRepositoryInterface)) {
            if (isset($parameters['translation']['fields'])) {
                $repositoryDefinition->addMethodCall('setTranslatableFields', array($parameters['translation']['fields']));
            }
        }

        $this->container->setDefinition(
            $this->getContainerKey('repository'),
            $repositoryDefinition
        );


        if (isset($parameters['classes']['factory'])) {
            $factoryDefinition = $this->getFactoryDefinition($parameters['classes']['factory'], $parameters['classes']['model']);

            $this->container->setDefinition(
                $this->getContainerKey('factory'),
                $factoryDefinition
            );
        }

        $this->setManagerAlias();
    }

    /**
     * Get the entity manager
     *
     * @return string
     */
    abstract protected function getManagerServiceKey();

    /**
     * Get the doctrine ClassMetadata class
     *
     * @return string
     */
    abstract protected function getClassMetadataClassname();

    /**
     * Get the respository service
     *
     * @param array $parameters
     *
     * @return Definition
     */
    abstract protected function getRepositoryDefinition(array $parameters);

    /**
     * @return Definition
     */
    protected function getConfigurationDefinition()
    {
        $definition = new Definition(new Parameter('sylius.controller.configuration.class'));
        $definition
            ->setFactory(array(new Reference('sylius.controller.configuration_factory'), 'createConfiguration'))
            ->setArguments(array($this->prefix, $this->resourceName, $this->templates))
            ->setPublic(false)
        ;

        return $definition;
    }

    /**
     * @param string $class
     *
     * @return Definition
     */
    protected function getControllerDefinition($class)
    {
        $definition = new Definition($class);
        $definition
            ->setArguments(array($this->getConfigurationDefinition()))
            ->addMethodCall('setContainer', array(new Reference('service_container')))
        ;

        return $definition;
    }

    /**
     * @param $factoryClass
     * @param $modelClass
     *
     * @return Definition
     */
    protected function getFactoryDefinition($factoryClass, $modelClass)
    {
        $translatableFactoryInterface = 'Sylius\Component\Translation\Factory\TranslatableFactoryInterface';

        $reflection = new \ReflectionClass($factoryClass);

        $definition = new Definition($factoryClass);

        if (interface_exists($translatableFactoryInterface) && $reflection->implementsInterface($translatableFactoryInterface)) {
            $decoratedDefinition = new Definition(Factory::class);
            $decoratedDefinition->setArguments(array($modelClass));

            $definition->setArguments(array($decoratedDefinition, new Reference('sylius.translation.locale_provider')));

            return $definition;
        }

        $definition->setArguments(array($modelClass));

        return $definition;
    }

    /**
     * @param mixed $models
     *
     * @return Definition
     */
    protected function getClassMetadataDefinition($models)
    {
        $definition = new Definition($this->getClassMetadataClassname());
        $definition
            ->setFactory(array(new Reference($this->getManagerServiceKey()), 'getClassMetadata'))
            ->setArguments(array($models))
            ->setPublic(false)
        ;

        return $definition;
    }

    protected function setManagerAlias()
    {
        $this->container->setAlias(
            $this->getContainerKey('manager'),
            new Alias($this->getManagerServiceKey())
        );
    }

    /**
     * @param string     $key
     * @param Definition $definition
     */
    protected function setDefinition($key, Definition $definition)
    {
        $this->container->setDefinition($this->getContainerKey($key), $definition);
    }

    /**
     * @param string $key
     * @param string $suffix
     *
     * @return string
     */
    protected function getContainerKey($key, $suffix = null)
    {
        return sprintf('%s.%s.%s%s', $this->prefix, $key, $this->resourceName, $suffix);
    }
}
