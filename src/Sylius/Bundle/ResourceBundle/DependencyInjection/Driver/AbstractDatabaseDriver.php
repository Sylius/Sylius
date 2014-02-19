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

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
abstract class AbstractDatabaseDriver implements DatabaseDriverInterface
{
    /**
     * @var ContainerBuilder $container
     */
    protected $container;

    /**
     * @var string $prefix
     */
    protected $prefix;

    /**
     * @var string $resourceName
     */
    protected $resourceName;

    /**
     * @var string $templates
     */
    protected $templates = null;

    public function __construct(ContainerBuilder $container, $prefix, $resourceName, $templates = null)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->resourceName = $resourceName;
        $this->templates = $templates;
    }

    abstract protected function getManagerServiceKey();
    abstract protected function getClassMetadataClassname();
    abstract protected function getRepositoryDefinition(array $classes);

    public function load(array $classes)
    {
        $this->container->setDefinition(
            $this->getContainerKey('controller'),
            $this->getControllerDefinition($classes['controller'])
        );

        $this->container->setDefinition(
            $this->getContainerKey('repository'),
            $this->getRepositoryDefinition($classes)
        );

        $this->setManagerAlias();
    }

    /**
     * @return Definition
     */
    protected function getConfirguationDefinition()
    {
        return (new Definition('Sylius\Bundle\ResourceBundle\Controller\Configuration'))
            ->setFactoryService('sylius.controller.configuration_factory')
            ->setFactoryMethod('createConfiguration')
            ->setArguments(array($this->prefix, $this->resourceName, $this->templates))
            ->setPublic(false)
        ;
    }

    /**
     * @param $class
     * @return Definition
     */
    protected function getControllerDefinition($class)
    {
        return (new Definition($class))
            ->setArguments(array($this->getConfirguationDefinition()))
            ->addMethodCall('setContainer', array(new Reference('service_container')))
        ;
    }

    /**
     * @param $models
     * @return Definition
     */
    protected function getClassMetadataDefinition($models)
    {
        return (new Definition($this->getClassMetadataClassname()))
            ->setFactoryService($this->getManagerServiceKey())
            ->setFactoryMethod('getClassMetadata')
            ->setArguments(array($models))
            ->setPublic(false)
        ;
    }

    protected function setManagerAlias()
    {
        $this->container->setAlias(
            $this->getContainerKey('manager'),
            new Alias($this->getManagerServiceKey())
        );
    }

    /**
     * @param $key
     * @param Definition $definition
     */
    protected function setDefinition($key, Definition $definition)
    {
        $this->container->setDefinition($this->getContainerKey($key), $definition);
    }

    /**
     * @param $key
     * @param  null   $suffix
     * @return string
     */
    protected function getContainerKey($key, $suffix = null)
    {
        return sprintf('%s.%s.%s%s', $this->prefix, $key, $this->resourceName, $suffix);
    }
}
