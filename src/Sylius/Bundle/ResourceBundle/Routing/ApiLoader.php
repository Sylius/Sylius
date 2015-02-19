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

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApiLoader implements LoaderInterface
{
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();
        list($prefix, $resource) = explode('.', $resource);

        $pluralResource = Inflector::pluralize($resource);
        $rootPath = '/'.$pluralResource.'/';
        $requirements = array();

        // GET collection request.
        $routeName = sprintf('%s_api_%s_index', $prefix, $resource);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:indexAction', $prefix, $resource),
        );

        $route = new Route($rootPath, $defaults, $requirements, array(), '', array(), array('GET'));
        $routes->add($routeName, $route);

        // GET request.
        $routeName = sprintf('%s_api_%s_show', $prefix, $resource);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:showAction', $prefix, $resource),
        );

        $route = new Route($rootPath.'{id}', $defaults, $requirements, array(), '', array(), array('GET'));
        $routes->add($routeName, $route);

        // POST request.
        $routeName = sprintf('%s_api_%s_create', $prefix, $resource);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:createAction', $prefix, $resource),
        );

        $route = new Route($rootPath, $defaults, $requirements, array(), '', array(), array('POST'));
        $routes->add($routeName, $route);

        // PUT request.
        $routeName = sprintf('%s_api_%s_update', $prefix, $resource);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:updateAction', $prefix, $resource),
        );

        $route = new Route($rootPath.'{id}', $defaults, $requirements, array(), '', array(), array('PUT', 'PATCH'));
        $routes->add($routeName, $route);

        // DELETE request.
        $routeName = sprintf('%s_api_%s_delete', $prefix, $resource);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:deleteAction', $prefix, $resource),
        );

        $route = new Route($rootPath.'{id}', $defaults, $requirements, array(), '', array(), array('DELETE'));
        $routes->add($routeName, $route);

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'sylius.api' === $type;
    }

    public function getResolver()
    {
        // Intentionally left blank
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank
    }
}
