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

class CrudLoader implements LoaderInterface
{
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();
        list($applicationName, $resourceName) = explode('.', $resource);

        $pluralResource = str_replace('_', '-', Inflector::pluralize($resourceName));
        $rootPath = '/'.$pluralResource.'/';
        $requirements = array();

        // GET collection request.
        $routeName = $indexRouteName = sprintf('%s_%s_grid', $applicationName, $resourceName);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:gridAction', $applicationName, $resourceName),
            '_sylius' => array(
                'template' => 'SyliusGridBundle:Crud:grid.html.twig'
            )
        );

        $route = new Route($rootPath, $defaults, $requirements, array(), '', array(), array('GET'));
        $routes->add($routeName, $route);

        // POST request.
        $routeName = sprintf('%s_%s_create', $applicationName, $resourceName);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:createAction', $applicationName, $resourceName),
            '_sylius' => array(
                'template' => 'SyliusGridBundle:Crud:create.html.twig',
                'redirect' => $indexRouteName
            )
        );

        $route = new Route($rootPath.'new', $defaults, $requirements, array(), '', array(), array('GET', 'POST'));
        $routes->add($routeName, $route);

        // PUT request.
        $routeName = sprintf('%s_%s_update', $applicationName, $resourceName);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:updateAction', $applicationName, $resourceName),
            '_sylius' => array(
                'template' => 'SyliusGridBundle:Crud:update.html.twig',
                'redirect' => $indexRouteName
            )
        );

        $route = new Route($rootPath.'{id}/edit', $defaults, $requirements, array(), '', array(), array('GET', 'PUT', 'PATCH'));
        $routes->add($routeName, $route);

        // DELETE request.
        $routeName = sprintf('%s_%s_delete', $applicationName, $resourceName);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:deleteAction', $applicationName, $resourceName),
            '_sylius' => array(
                'redirect' => $indexRouteName
            )
        );

        $route = new Route($rootPath.'{id}', $defaults, $requirements, array(), '', array(), array('DELETE'));
        $routes->add($routeName, $route);

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'sylius.crud' === $type;
    }

    public function getResolver()
    {
        // Intentionally left blank.
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank.
    }
}
