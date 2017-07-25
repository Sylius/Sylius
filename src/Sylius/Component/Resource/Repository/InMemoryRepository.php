<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Resource\Repository;

use ArrayObject;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\Exception\ExistingResourceException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class InMemoryRepository implements RepositoryInterface
{
    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var ArrayObject
     */
    protected $arrayObject;

    /**
     * @var string
     */
    protected $interface;

    /**
     * @param string $interface | Fully qualified name of the interface.
     *
     * @throws \InvalidArgumentException
     * @throws UnexpectedTypeException
     */
    public function __construct($interface)
    {
        if (null === $interface) {
            throw new \InvalidArgumentException('Resource\'s interface needs to be stated.');
        }

        if (!in_array(ResourceInterface::class, class_implements($interface))) {
            throw new UnexpectedTypeException($interface, ResourceInterface::class);
        }

        $this->interface = $interface;
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->arrayObject = new ArrayObject();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ExistingResourceException
     * @throws UnexpectedTypeException
     */
    public function add(ResourceInterface $resource)
    {
        if (!$resource instanceof $this->interface) {
            throw new UnexpectedTypeException($resource, $this->interface);
        }

        if (in_array($resource, $this->findAll())) {
            throw new ExistingResourceException();
        }

        $this->arrayObject->append($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource)
    {
        $newResources = array_filter($this->findAll(), function ($object) use ($resource) {
            return $object !== $resource;
        });

        $this->arrayObject->exchangeArray($newResources);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnsupportedMethodException
     */
    public function find($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->arrayObject->getArrayCopy();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $results = $this->findAll();

        if (!empty($criteria)) {
            $results = $this->applyCriteria($results, $criteria);
        }

        if (!empty($orderBy)) {
            $results = $this->applyOrder($results, $orderBy);
        }

        $results = array_slice($results, $offset, $limit);

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function findOneBy(array $criteria)
    {
        if (empty($criteria)) {
            throw new \InvalidArgumentException('The criteria array needs to be set.');
        }

        $results = $this->applyCriteria($this->findAll(), $criteria);

        if ($result = reset($results)) {
            return $result;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->interface;
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = [], array $sorting = [])
    {
        $resources = $this->findAll();

        if (!empty($sorting)) {
            $resources = $this->applyOrder($resources, $sorting);
        }

        if (!empty($criteria)) {
            $resources = $this->applyCriteria($resources, $criteria);
        }

        $adapter = new ArrayAdapter($resources);
        $pagerfanta = new Pagerfanta($adapter);

        return $pagerfanta;
    }

    /**
     * @param ResourceInterface[] $resources
     * @param array               $criteria
     *
     * @return ResourceInterface[]|array
     */
    private function applyCriteria(array $resources, array $criteria)
    {
        foreach ($this->arrayObject as $object) {
            foreach ($criteria as $criterion => $value) {
                if ($value !== $this->accessor->getValue($object, $criterion)) {
                    $key = array_search($object, $resources);
                    unset($resources[$key]);
                }
            }
        }

        return $resources;
    }

    /**
     * @param ResourceInterface[] $resources
     * @param array               $orderBy
     *
     * @return ResourceInterface[]
     */
    private function applyOrder(array $resources, array $orderBy)
    {
        $results = $resources;

        foreach ($orderBy as $property => $order) {
            $sortable = [];

            foreach ($results as $key => $object) {
                $sortable[$key] = $this->accessor->getValue($object, $property);
            }

            if (RepositoryInterface::ORDER_ASCENDING === $order) {
                asort($sortable);
            }
            if (RepositoryInterface::ORDER_DESCENDING === $order) {
                arsort($sortable);
            }

            $results = [];

            foreach ($sortable as $key => $propertyValue) {
                $results[$key] = $resources[$key];
            }
        }

        return $results;
    }
}
