<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing\Builder;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RouteCollectionBuilder implements RouteCollectionBuilderInterface
{
    /**
     * @var string
     */
    protected $application;
    /**
     * @var string|null
     */
    protected $prefix = null;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * {@inheritdoc}
     */
    public function createCollection($application, $prefix = null)
    {
        $this->application = $application;
        $this->prefix = $prefix;
        $this->routes = new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function add($resource, $action, array $method)
    {
        $routeName = $this->getRouteName($resource, $action);
        $rootPath = $this->getRoutePath($resource, $action);
        $defaults = $this->getDefault($resource, $action);
        $route = new Route($rootPath, $defaults, array(), array(), '', array(), $method);

        $this->routes->add($routeName, $route);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->routes;
    }

    /**
     * @param string $resource
     * @param string $action
     *
     * @return array
     */
    protected function getDefault($resource, $action)
    {
        return array(
            '_controller' => sprintf('%s.controller.%s:%sAction', $this->application, $resource, $action),
        );
    }

    /**
     * @param string $resource
     * @param string $action
     *
     * @return string
     */
    protected function getRoutePath($resource, $action)
    {
        $suffix = null;
        $pluralResource = Inflector::pluralize($resource);

        if (in_array($action, array('update', 'delete'))) {
            $suffix = '{id}';
        }


        return sprintf('/%s/%s', $pluralResource, $suffix);
    }

    /**
     * @param string $resource
     * @param string $action
     *
     * @return string
     */
    protected function getRouteName($resource, $action)
    {
        if (null !== $this->prefix) {
            $resource = sprintf('%s_%s', $this->prefix, $resource);
        }

        return sprintf('%s_%s_%s', $this->application, $resource, $action);
    }
}
