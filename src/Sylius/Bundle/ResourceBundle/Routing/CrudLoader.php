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
use Symfony\Component\Yaml\Parser as YamlParser;

class CrudLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $routingDir;

    /**
     * Construtor
     *
     * @param string $routingDir
     */
    public function __construct($routingDir)
    {
        $this->routingDir = $routingDir;
    }

    /**
     * @param string $resource
     * @param string $type
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if ('yml' === pathinfo($resource, PATHINFO_EXTENSION)) {
            $configuration = $this->loadFile($resource);
        }

        $section = (isset($configuration['section'])) ? $configuration['section'] : '';
        $resource = (isset($configuration['resource'])) ? $configuration['resource'] : $resource;
        $templates = $this->generateRouteTemplates((isset($configuration['templates'])) ? $configuration['templates'] : array());

        $routes = new RouteCollection();
        list($applicationName, $resourceName) = explode('.', $resource);

        $pluralResource = str_replace('_', '-', Inflector::pluralize($resourceName));
        $rootPath = '/'.$pluralResource.'/';
        $requirements = array();

        // GET collection request.
        $gridRouteParameters = array('template' => $templates['grid']);
        if (isset($configuration['grid'])) {
            $gridRouteParameters['grid'] = $configuration['grid'];
        }

        $gridRoute = $this->generateRoute('grid', '', $applicationName, $section, $resourceName, $rootPath, $gridRouteParameters, array('GET'));
        $routes->add($gridRoute['name'], $gridRoute['route']);
        $indexRouteName = $gridRoute['name'];

        // POST request.
        $createRoute = $this->generateRoute('create', 'new', $applicationName, $section, $resourceName, $rootPath, array('template' => $templates['create'], 'redirect' => $indexRouteName), array('GET', 'POST'));
        $routes->add($createRoute['name'], $createRoute['route']);

        // Mass action.
        $massRoute = $this->generateRoute('mass', 'mass', $applicationName, $section, $resourceName, $rootPath, array(), array('POST'));
        $routes->add($massRoute['name'], $massRoute['route']);

        // PUT request.
        $updateRoute = $this->generateRoute('update', '{id}/edit', $applicationName, $section, $resourceName, $rootPath, array('template' => $templates['update'], 'redirect' => $indexRouteName), array('GET', 'PUT', 'PATCH'));
        $routes->add($updateRoute['name'], $updateRoute['route']);

        // DELETE request.
        $deleteRoute = $this->generateRoute('delete', '{id}', $applicationName, $section, $resourceName, $rootPath, array('redirect' => $indexRouteName), array('DELETE'));
        $routes->add($deleteRoute['name'], $deleteRoute['route']);

        return $routes;
    }

    /**
     * @param string $resource
     * @param string $type
     *
     * @return boolean
     */
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

    /**
     * @param array $customTemplates
     *
     * @return array
     */
    private function generateRouteTemplates(array $customTemplates)
    {
        $templates = array();

        $templates['grid'] = (isset($customTemplates['grid'])) ? $customTemplates['grid'] : 'SyliusGridBundle:Crud:grid.html.twig';
        $templates['create'] = (isset($customTemplates['create'])) ? $customTemplates['create'] : 'SyliusGridBundle:Crud:create.html.twig';
        $templates['update'] = (isset($customTemplates['update'])) ? $customTemplates['update'] : 'SyliusGridBundle:Crud:update.html.twig';

        return $templates;
    }

    /**
     * Generates route with given parameters, returns array with Route object and route name string
     *
     * @param string $routeName
     * @param string $routePathSuffix
     * @param string $applicationName
     * @param string $section
     * @param string $resourceName
     * @param string $rootPath
     * @param array  $routeParameters
     * @param array  $routeMethods
     *
     * @return array
     */
    private function generateRoute($routeName, $routePathSuffix, $applicationName, $section, $resourceName, $rootPath, array $routeParameters, array $routeMethods)
    {
        $sectionPrefix = empty($section) ? '' : '_'.$section;

        $routeFullName = sprintf('%s%s_%s_%s', $applicationName, $sectionPrefix, $resourceName, $routeName);
        $defaults = array(
            '_controller' => sprintf('%s.controller.%s:%sAction', $applicationName, $resourceName, $routeName),
            '_sylius' => array_merge($routeParameters, array('section' => $section)),
        );

        $route = new Route($rootPath.$routePathSuffix, $defaults, array(), array(), '', array(), $routeMethods);

        return array(
            'name'  => $routeFullName,
            'route' => $route,
        );
    }

    /**
     * @param string $filePath
     * 
     * @return array
     */
    private function loadFile($filePath)
    {
        $path = $this->routingDir.$filePath;

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        $yamlParser = new YamlParser();

        return $yamlParser->parse(file_get_contents($path));
    }
}
