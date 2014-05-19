<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Routing;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\DoctrineProvider;

class RouteProvider extends DoctrineProvider implements RouteProviderInterface
{
    /**
     * Route configuration for the object classes to search in
     *
     * @var array
     */
    protected $routeConfigs;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param array $routeConfigs
     */
    public function __construct(ManagerRegistry $managerRegistry, array $routeConfigs)
    {
        $this->routeConfigs = $routeConfigs;
        parent::__construct($managerRegistry);
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteByName($name, $parameters = array())
    {
        if (is_object($name)) {
            $className = ClassUtils::getClass($name);
            if (isset($this->routeConfigs[$className])) {
                return $this->createRouteFromEntity($name);
            }
        }

        $repositories = $this->getRepositories();
        foreach ($repositories as $className => $repository) {
            $entity = $repository->findOneBy(array($this->routeConfigs[$className]['field'] => $name));
            if ($entity) {
                return $this->createRouteFromEntity($entity);
            }
        }

        throw new RouteNotFoundException("No route found for name '$name'");
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutesByNames($names = null)
    {
        if (null === $names) {
            if (0 === $this->routeCollectionLimit) {
                return array();
            }

            $collection = new RouteCollection();
            $repositories = $this->getRepositories();
            foreach ($repositories as $className => $repository) {
                $entities = $repository->findBy(array(), null, $this->routeCollectionLimit ?: null);
                foreach ($entities as $entity) {
                    $name = $this->getFieldValue($entity, $this->routeConfigs[$className]['field']);
                    $collection->add($name, $this->createRouteFromEntity($entity));
                }
            }

            return $collection;
        }

        $routes = array();
        foreach ($names as $name) {
            try {
                $routes[] = $this->getRouteByName($name);
            } catch (RouteNotFoundException $e) {
                // not found
            }
        }

        return $routes;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        $path = $request->getPathInfo();
        $collection = new RouteCollection();

        if (empty($path)) {
            return $collection;
        }

        $repositories = $this->getRepositories();
        foreach ($repositories as $className => $repository) {
            if ('' === $this->routeConfigs[$className]['prefix']
                || 0 === strpos($path, $this->routeConfigs[$className]['prefix'])
            ) {
                $name = substr($path, strlen($this->routeConfigs[$className]['prefix']));
                $name = trim($name, '/');
                $entity = $repository->findOneBy(array($this->routeConfigs[$className]['field'] => $name));
                if (!$entity) {
                    continue;
                }

                $route = $this->createRouteFromEntity($entity);
                if (preg_match('/.+\.([a-z]+)$/i', $name, $matches)) {
                    $route->setDefault('_format', $matches[1]);
                }

                $collection->add($name, $route);
            }
        }

        return $collection;
    }

    /**
     * @return array
     */
    protected function getRepositories()
    {
        $om = $this->getObjectManager();
        $repositories = array();
        foreach ($this->routeConfigs as $className => $foo) {
            $repositories[$className] = $om->getRepository($className);
        }

        return $repositories;
    }

    /**
     * @param $entity
     * @param $fieldName
     *
     * @return string
     */
    private function getFieldValue($entity, $fieldName)
    {
        return $entity->{'get'.ucfirst($fieldName)}();
    }

    /**
     * @param $entity
     *
     * @return Route
     */
    private function createRouteFromEntity($entity)
    {
        $className = ClassUtils::getClass($entity);
        $fieldName = $this->routeConfigs[$className]['field'];
        $value = $this->getFieldValue($entity, $fieldName);
        $defaults = array('_sylius_entity' => $entity, $fieldName => $value);

        return new Route($this->routeConfigs[$className]['prefix'].'/'.$value, $defaults);
    }
}
