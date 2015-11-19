<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Repository;

use ArrayObject;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\ResourceInterface;
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
     * @param string $interface
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
     * @param ResourceInterface $resource
     *
     * @throws \InvalidArgumentException
     * @throws UnexpectedTypeException
     */
    public function add(ResourceInterface $resource)
    {
        if (!in_array($this->interface, class_implements($resource))) {
            throw new UnexpectedTypeException($resource, $this->interface);
        }

        if (null === $resource->getId()) {
            throw new \InvalidArgumentException('Resource\'s id needs to be set in order to add.');
        }

        if ($this->arrayObject->offsetExists($resource->getId())) {
            throw new \InvalidArgumentException(
                sprintf('An object with id \'%s\' is already in the repository.', $resource->getId())
            );
        }

        $this->arrayObject->offsetSet($resource->getId(), $resource);
    }

    /**
     * @param ResourceInterface $resource
     */
    public function remove(ResourceInterface $resource)
    {
        if ($this->arrayObject->offsetExists($resource->getId())) {
            $this->arrayObject->offsetUnset($resource->getId());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        if (empty($id)) {
            return null;
        }

        if ($this->arrayObject->offsetExists($id)) {
            return $this->arrayObject->offsetGet($id);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return array_values($this->arrayObject->getArrayCopy());
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $results = $this->arrayObject->getArrayCopy();

        if (isset($criteria)) {
            $results = $this->applyCriteria($results, $criteria);
        }

        if (isset($orderBy)) {
            $results = $this->applyOrder($results, $orderBy);
        }

        $results = array_slice($results, $offset, $limit);

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \UnexpectedValueException
     */
    public function findOneBy(array $criteria)
    {
        $results = $this->applyCriteria($this->findAll(), $criteria);

        if (1 < $length = count($results)) {
            throw new \UnexpectedValueException(sprintf('Non unique result. Number of results: %s.', $length));
        }

        return $results[0];
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
    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        $results = $this->findAll();

        if (isset($orderBy)) {
            $results = $this->applyOrder($results, $orderBy);
        }

        if (isset($criteria)) {
            $results = $this->applyCriteria($results, $criteria);
        }

        $adapter = new ArrayAdapter($results);
        $pagerfanta = new Pagerfanta($adapter);

        return $pagerfanta;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
    }

    /**
     * @param ResourceInterface[] $resources
     * @param array               $criteria
     *
     * @return ResourceInterface[]|array
     */
    private function applyCriteria(array $resources, array $criteria = array())
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
        $property = key($orderBy);
        $order = $orderBy[$property];

        $sortable = array();
        $results = array();

        foreach ($resources as $object) {
            $sortable[$object->getId()] = $this->accessor->getValue($object, $property);
        }

        if ('ASC' === $order) {
            asort($sortable);
        }
        if ('DSC' === $order) {
            arsort($sortable);
        }

        foreach ($sortable as $id => $value) {
            $results[$id] = $resources[$id];
        }

        return $results;
    }
}
