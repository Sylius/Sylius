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
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
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
        $configuration = Yaml::parse($resource);
        $metadata = $this->resourceRegistry->get($configuration['alias']);
        $routes = $this->routeFactory->createRouteCollection();

        $rootPath = sprintf('/%s/', isset($configuration['path']) ? $configuration['path'] : Urlizer::urlize($metadata->getPluralName()));

        $indexRoute = $this->createRoute($metadata, $configuration, $rootPath, 'index', array('GET'));
        $routes->add($this->getRouteName($metadata, $configuration, 'index'), $indexRoute);

        $createRoute = $this->createRoute($metadata, $configuration, $rootPath.'new', 'create', array('GET', 'POST'));
        $routes->add($this->getRouteName($metadata, $configuration, 'create'), $createRoute);

        $updateRoute = $this->createRoute($metadata, $configuration, $rootPath.'{id}/edit', 'update', array('GET', 'PUT', 'PATCH'));
        $routes->add($this->getRouteName($metadata, $configuration, 'update'), $updateRoute);

        $showRoute = $this->createRoute($metadata, $configuration, $rootPath.'{id}', 'show', array('GET'));
        $routes->add($this->getRouteName($metadata, $configuration, 'show'), $showRoute);

        $deleteRoute = $this->createRoute($metadata, $configuration, $rootPath.'{id}', 'delete', array('DELETE'));
        $routes->add($this->getRouteName($metadata, $configuration, 'delete'), $deleteRoute);

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'sylius.resource' === $type;
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
     * @param string $actionName
     * @param array $default
     * @param array $methods
     *
     * @return Route
     */
    private function createRoute(MetadataInterface $metadata, array $configuration, $path, $actionName, array $methods)
    {
        $defaults = array(
            '_controller' => $metadata->getServiceId('controller').sprintf(':%sAction', $actionName)
        );

        if (isset($configuration['form']) && in_array($actionName, array('create', 'update'))) {
            $defaults['_sylius']['form'] = $configuration['form'];
        }
        if (isset($configuration['section'])) {
            $defaults['_sylius']['section'] = $configuration['section'];
        }
        if (isset($configuration['templates']) && in_array($actionName, array('show', 'index', 'create', 'update'))) {
            $defaults['_sylius']['template'] = sprintf('%s:%s.html.twig', $configuration['templates'], $actionName);
        }

        return $this->routeFactory->createRoute($path, $defaults, array(), array(), '', array(), $methods);
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
