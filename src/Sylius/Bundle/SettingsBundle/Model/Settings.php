<?php

namespace Sylius\Bundle\SettingsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Settings implements SettingsInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var ArrayCollection|ParameterInterface[]
     */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema($schema)
    {
        if (null !== $this->schema) {
            throw new \LogicException('A settings schema is immutable, you have to define a new "Setting" object to use another schema.');
        }

        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $parameters = [];
        foreach ($this->parameters as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->set($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter with name "%s" does not exist.', $name));
        }

        return $this->parameters->get($name)->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return $this->parameters->containsKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if (!$this->has($name)) {
            $parameter = new Parameter();

            $parameter->setSettings($this);
            $parameter->setName($name);
            $parameter->setValue($value);

            $this->parameters->set($name, $parameter);
        } else {
            $parameter = $this->parameters->get($name);
            $parameter->setValue($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            $this->parameters->remove($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getParameters());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->parameters->count();
    }
}
