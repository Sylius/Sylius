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
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Util\ClassUtils;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\DoctrineProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RouteProvider extends DoctrineProvider implements RouteProviderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $routeConfigs;

    /**
     * @var ObjectRepository[]
     */
    protected $classRepositories = [];

    /**
     * @param ContainerInterface $container
     * @param ManagerRegistry $managerRegistry
     * @param array $routeConfigs
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $managerRegistry, array $routeConfigs)
    {
        $this->container = $container;
        $this->routeConfigs = $routeConfigs;
        $this->classRepositories = [];

        parent::__construct($managerRegistry);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteByName($name, $parameters = [])
    {
        if (is_object($name)) {
            $className = ClassUtils::getClass($name);
            if (isset($this->routeConfigs[$className])) {
                return $this->createRouteFromEntity($name);
            }
        }

        foreach ($this->getRepositories() as $className => $repository) {
            $entity = $this->tryToFindEntity($name, $repository, $className);
            if ($entity) {
                return $this->createRouteFromEntity($entity);
            }
        }

        throw new RouteNotFoundException("No route found for name '$name'");
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutesByNames($names = null)
    {
        if (null === $names) {
            if (0 === $this->routeCollectionLimit) {
                return [];
            }

            $collection = new RouteCollection();
            foreach ($this->getRepositories() as $className => $repository) {
                $entities = $repository->findBy([], null, $this->routeCollectionLimit ?: null);
                foreach ($entities as $entity) {
                    $name = $this->getFieldValue($entity, $this->routeConfigs[$className]['field']);
                    $collection->add($name, $this->createRouteFromEntity($entity));
                }
            }

            return $collection;
        }

        $routes = [];
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
     * {@inheritdoc}
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        $path = $request->getPathInfo();
        $collection = new RouteCollection();

        if (empty($path)) {
            return $collection;
        }

        foreach ($this->getRepositories() as $className => $repository) {
            if ('' === $this->routeConfigs[$className]['prefix']
                || 0 === strpos($path, $this->routeConfigs[$className]['prefix'])
            ) {
                $value = substr($path, strlen($this->routeConfigs[$className]['prefix']));
                $value = trim($value, '/');
                $value = urldecode($value);

                if (empty($value)) {
                    continue;
                }

                $entity = $this->tryToFindEntity($value, $repository, $className);

                if (null === $entity) {
                    continue;
                }

                $route = $this->createRouteFromEntity($entity, $value);
                if (preg_match('/.+\.([a-z]+)$/i', $value, $matches)) {
                    $route->setDefault('_format', $matches[1]);
                }

                $collection->add($value, $route);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function addRepository($class, $id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('Expected service id!');
        }

        $this->classRepositories[$class] = $id;
    }

    /**
     * @return array
     */
    private function getRepositories()
    {
        $repositories = [];

        foreach ($this->classRepositories as $class => $id) {
            $repositories[$class] = $this->container->get($id);
        }

        return $repositories;
    }

    /**
     * @param object $entity
     * @param string $fieldName
     *
     * @return string
     */
    private function getFieldValue($entity, $fieldName)
    {
        return $entity->{'get'.ucfirst($fieldName)}();
    }

    /**
     * @param object $entity
     *
     * @return Route
     */
    private function createRouteFromEntity($entity, $value = null)
    {
        $className = ClassUtils::getClass($entity);
        $fieldName = $this->routeConfigs[$className]['field'];

        if (null === $value) {
            $value = $this->getFieldValue($entity, $fieldName);
        }
        $defaults = ['_sylius_entity' => $entity, $fieldName => $value];

        return new Route($this->routeConfigs[$className]['prefix'].'/'.$value, $defaults);
    }

    /**
     * @param string $identifier
     * @param RepositoryInterface $repository
     * @param string $className
     *
     * @return object|null
     */
    private function tryToFindEntity($identifier, RepositoryInterface $repository, $className)
    {
        if ('slug' === $this->routeConfigs[$className]['field']) {
            return $repository->findOneBySlug($identifier);
        }
        if ('name' === $this->routeConfigs[$className]['field']) {
            return $repository->findOneByName($identifier);
        }
        if ('permalink' === $this->routeConfigs[$className]['field']) {
            return $repository->findOneByPermalink($identifier);
        }

        return $repository->findOneBy([$this->routeConfigs[$className]['field'] => $identifier]);
    }
}
