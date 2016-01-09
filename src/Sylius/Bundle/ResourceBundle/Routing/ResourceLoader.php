<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing;

use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceLoader implements LoaderInterface
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var RouteFactoryInterface
     */
    private $routeFactory;

    /**
     * @param RegistryInterface $resourceRegistry
     * @param RouteFactoryInterface $routeFactory
     */
    public function __construct(RegistryInterface $resourceRegistry, RouteFactoryInterface $routeFactory)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->routeFactory = $routeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $processor = new Processor();
        $configurationDefinition = new Configuration();

        $configuration = Yaml::parse($resource);
        $configuration = $processor->processConfiguration($configurationDefinition, ['routing' => $configuration]);

        if (!empty($configuration['only']) && !empty($configuration['except'])) {
            throw new \InvalidArgumentException('You can configure only one of "except" & "only" options.');
        }

        $routesToGenerate = ['show', 'index', 'create', 'update', 'delete'];

        if (!empty($configuration['only'])) {
            $routesToGenerate = $configuration['only'];
        }
        if (!empty($configuration['except'])) {
            $routesToGenerate = array_diff($routesToGenerate, $configuration['except']);
        }

        $isApi = $type === 'sylius.resource_api';

        $metadata = $this->resourceRegistry->get($configuration['alias']);
        $routes = $this->routeFactory->createRouteCollection();

        $rootPath = sprintf('/%s/', isset($configuration['path']) ? $configuration['path'] : Urlizer::urlize($metadata->getPluralName()));

        if (in_array('index', $routesToGenerate)) {
            $indexRoute = $this->createRoute($metadata, $configuration, $rootPath, 'index', ['GET'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'index'), $indexRoute);
        }

        if (in_array('create', $routesToGenerate)) {
            $createRoute = $this->createRoute($metadata, $configuration, $isApi ? $rootPath : $rootPath . 'new', 'create', $isApi ? ['POST'] : ['GET', 'POST'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'create'), $createRoute);
        }

        if (in_array('update', $routesToGenerate)) {
            $updateRoute = $this->createRoute($metadata, $configuration, $isApi ? $rootPath . '{id}' : $rootPath . '{id}/edit', 'update', $isApi ? ['PUT', 'PATCH'] : ['GET', 'PUT', 'PATCH'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'update'), $updateRoute);
        }

        if (in_array('show', $routesToGenerate)) {
            $showRoute = $this->createRoute($metadata, $configuration, $rootPath . '{id}', 'show', ['GET'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'show'), $showRoute);
        }

        if (in_array('delete', $routesToGenerate)) {
            $deleteRoute = $this->createRoute($metadata, $configuration, $rootPath . '{id}', 'delete', array('DELETE'), $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'delete'), $deleteRoute);
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'sylius.resource' === $type || 'sylius.resource_api' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        // Intentionally left blank.
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank.
    }

    /**
     * @param MetadataInterface $metadata
     * @param array $configuration
     * @param string $path
     * @param string $actionName
     * @param array $methods
     *
     * @return Route
     */
    private function createRoute(MetadataInterface $metadata, array $configuration, $path, $actionName, array $methods, $isApi = false)
    {
        $defaults = [
            '_controller' => $metadata->getServiceId('controller').sprintf(':%sAction', $actionName),
        ];

        if ($isApi && 'index' === $actionName) {
            $defaults['_sylius']['serialization_groups'] = ['Default'];
        }
        if ($isApi && in_array($actionName, ['show', 'create', 'update'])) {
            $defaults['_sylius']['serialization_groups'] = ['Default', 'Detailed'];
        }
        if (isset($configuration['grid']) && 'index' === $actionName) {
            $defaults['_sylius']['grid'] = $configuration['grid'];
        }
        if (isset($configuration['form']) && in_array($actionName, ['create', 'update'])) {
            $defaults['_sylius']['form'] = $configuration['form'];
        }
        if (isset($configuration['section'])) {
            $defaults['_sylius']['section'] = $configuration['section'];
        }
        if (isset($configuration['templates']) && in_array($actionName, ['show', 'index', 'create', 'update'])) {
            $defaults['_sylius']['template'] = sprintf('%s:%s.html.twig', $configuration['templates'], $actionName);
        }
        if (isset($configuration['redirect']) && in_array($actionName, ['create', 'update'])) {
            $defaults['_sylius']['redirect'] = $this->getRouteName($metadata, $configuration, $configuration['redirect']);
        }
        if (isset($configuration['vars']['all'])) {
            $defaults['_sylius']['vars'] = $configuration['vars']['all'];
        }
        if (isset($configuration['vars'][$actionName])) {
            $vars = isset($configuration['vars']['all']) ? $configuration['vars']['all'] : [];
            $defaults['_sylius']['vars'] = array_merge($vars, $configuration['vars'][$actionName]);
        }

        return $this->routeFactory->createRoute($path, $defaults, [], [], '', [], $methods);
    }

    /**
     * @param MetadataInterface $metadata
     * @param array $configuration
     * @param string $actionName
     *
     * @return string
     */
    private function getRouteName(MetadataInterface $metadata, array $configuration, $actionName)
    {
        $sectionPrefix = isset($configuration['section']) ? $configuration['section'].'_' : '';

        return sprintf('%s_%s%s_%s', $metadata->getApplicationName(), $sectionPrefix, $metadata->getName(), $actionName);
    }
}
